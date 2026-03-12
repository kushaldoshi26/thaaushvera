<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiController extends Controller
{
    public function showAiPage()
    {
        return view('admin.ai.dashboard');
    }

    /**
     * Main AI agent endpoint — calls OpenAI directly, falls back to AI server
     */
    public function askAi(Request $request)
    {
        $user     = auth()->user();
        $mode     = $request->input('mode', 'chat');
        $message  = $request->input('message', '');

        // Role restriction for debug mode
        if ($mode === 'debug' && $user && !$user->isSuperAdmin()) {
            return response()->json(['error' => 'Debug AI is restricted to Super Admin only.'], 403);
        }

        // Usage limit check (only if user is authenticated)
        if ($user && $user->ai_used >= $user->ai_limit) {
            return response()->json(['error' => 'AI usage limit reached. Contact your admin.'], 403);
        }

        // ── Try direct OpenAI API ──────────────────────────────────────────
        $openaiKey = env('OPENAI_API_KEY');
        $geminiKey = env('GEMINI_API_KEY');

        if ($openaiKey) {
            return $this->callOpenAI($request, $openaiKey, $user, $mode, $message);
        }

        if ($geminiKey) {
            return $this->callGemini($request, $geminiKey, $user, $mode, $message);
        }

        // ── Fallback: try external AI server ──────────────────────────────
        $aiServerUrl = env('AI_SERVER_URL', 'http://127.0.0.1:9000');
        try {
            $response = Http::timeout(15)->post($aiServerUrl . '/ai-agent', [
                'message' => $message,
                'data'    => $request->input('data', []),
                'mode'    => $mode,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $this->logAndTrack($result, $request, $user, $mode);
                return response()->json($result);
            }
        } catch (\Exception $e) {
            // fall through to no-key response
        }

        // ── No API key, no server — return helpful message ─────────────────
        return response()->json([
            'content' => $this->getNoKeyResponse($mode, $message),
            'mode'    => $mode,
            'source'  => 'demo',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OpenAI
    // ─────────────────────────────────────────────────────────────────────────
    private function callOpenAI(Request $request, string $key, $user, string $mode, string $message)
    {
        try {
            // Image generation modes
            if (in_array($mode, ['image', 'banner'])) {
                $response = Http::timeout(60)
                    ->withToken($key)
                    ->post('https://api.openai.com/v1/images/generations', [
                        'model'   => 'dall-e-3',
                        'prompt'  => $message,
                        'n'       => 1,
                        'size'    => '1024x1024',
                        'quality' => 'standard',
                    ]);

                if ($response->failed()) {
                    return response()->json([
                        'error'   => 'OpenAI image generation failed: ' . ($response->json()['error']['message'] ?? 'Unknown error'),
                        'content' => $message,
                    ], 200);
                }

                $imageUrl  = $response->json()['data'][0]['url'] ?? null;
                $savedPath = $imageUrl ? $this->downloadAndSaveImage($imageUrl) : null;

                $result = [
                    'image_url'        => $imageUrl,
                    'saved_image_path' => $savedPath ? asset('uploads/ai-images/' . basename($savedPath)) : null,
                    'mode'             => $mode,
                    'source'           => 'openai-dalle3',
                ];

                $this->logAndTrack($result, $request, $user, $mode);
                return response()->json($result);
            }

            // Text / chat modes
            $systemPrompt = $this->getSystemPrompt($mode);
            $history      = $request->input('history', []);

            $messages = [['role' => 'system', 'content' => $systemPrompt]];
            foreach (array_slice($history, -8) as $h) {
                $messages[] = ['role' => $h['role'], 'content' => $h['content']];
            }
            $messages[] = ['role' => 'user', 'content' => $message];

            $response = Http::timeout(60)
                ->withToken($key)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => 'gpt-4o-mini',
                    'messages'    => $messages,
                    'max_tokens'  => 1500,
                    'temperature' => 0.8,
                ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'OpenAI error: ' . ($response->json()['error']['message'] ?? 'Unknown'),
                ], 200);
            }

            $data   = $response->json();
            $text   = $data['choices'][0]['message']['content'] ?? '';
            $tokens = $data['usage'] ?? [];

            $result = [
                'content'       => $text,
                'input_tokens'  => $tokens['prompt_tokens'] ?? null,
                'output_tokens' => $tokens['completion_tokens'] ?? null,
                'cost_estimate' => isset($tokens['total_tokens']) ? round($tokens['total_tokens'] * 0.00000015, 6) : null,
                'mode'          => $mode,
                'source'        => 'openai-gpt4o-mini',
            ];

            $this->logAndTrack($result, $request, $user, $mode);
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'OpenAI connection failed: ' . $e->getMessage(),
                'content' => $this->getNoKeyResponse($mode, $message),
            ], 200);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Google Gemini
    // ─────────────────────────────────────────────────────────────────────────
    private function callGemini(Request $request, string $key, $user, string $mode, string $message)
    {
        try {
            $systemPrompt = $this->getSystemPrompt($mode);
            $fullPrompt   = $systemPrompt . "\n\nUser: " . $message;

            // Use gemini-2.0-flash on v1 API (confirmed available for this key)
            $url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent?key={$key}";

            $response = Http::timeout(60)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        ['parts' => [['text' => $fullPrompt]]]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 1500,
                        'temperature'     => 0.8,
                    ],
                ]);

            if ($response->failed()) {
                $errBody = $response->json();
                $errMsg  = $errBody['error']['message'] ?? ('HTTP ' . $response->status());
                return response()->json([
                    'error'   => 'Gemini error: ' . $errMsg,
                    'content' => $this->getNoKeyResponse($mode, $message),
                ], 200);
            }

            $text   = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
            if (empty($text)) {
                return response()->json([
                    'error'   => 'Gemini returned an empty response.',
                    'content' => $this->getNoKeyResponse($mode, $message),
                ], 200);
            }

            $result = ['content' => $text, 'mode' => $mode, 'source' => 'gemini-2.0-flash'];
            $this->logAndTrack($result, $request, $user, $mode);
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Gemini connection failed: ' . $e->getMessage(),
                'content' => $this->getNoKeyResponse($mode, $message),
            ], 200);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────
    private function getSystemPrompt(string $mode): string
    {
        $brand = 'AUSHVERA is a premium Ayurvedic wellness brand from India selling herbal teas, botanical tonics, wellness drops, and ritual products. The brand values: natural ingredients, ancient Ayurvedic wisdom, modern presentation, luxury feel, and holistic health. Target customers: health-conscious Indians aged 25-45.';

        return match ($mode) {
            'banner', 'image' => "You are a creative director for $brand. Generate detailed image generation prompts.",
            'scheme'   => "You are a marketing strategist for $brand. Generate detailed marketing campaigns, seasonal schemes, discount structures, and promotional plans. Be very specific with actual discount percentages, timelines, and copy.",
            'content'  => "You are a premium copywriter for $brand. Write compelling, conversion-focused content. Use elegant language that reflects the brand's Ayurvedic heritage and luxury positioning.",
            'chat'     => "You are the AI business assistant for $brand. Help with business strategy, product ideas, pricing, customer insights, marketing, and operations. Be concise and actionable.",
            'business' => "You are a business intelligence analyst for $brand. Provide data-driven insights, market analysis, and strategic recommendations.",
            'debug'    => "You are a technical debugger. Analyze system issues and provide solutions.",
            default    => "You are a helpful AI assistant for $brand. Be professional, creative, and brand-aligned.",
        };
    }

    private function getNoKeyResponse(string $mode, string $message): string
    {
        return match ($mode) {
            'ping'   => 'pong',
            'banner', 'image' => "🎨 **Banner Prompt Ready!**\n\nYour prompt has been crafted:\n\n> {$message}\n\n**To generate the image:**\n1. Copy the prompt above\n2. Go to [DALL-E](https://chat.openai.com) or [Midjourney](https://midjourney.com)\n3. Paste and generate\n\n**OR** add `OPENAI_API_KEY=sk-...` to your `.env` file for automatic image generation here.",
            'scheme' => "📋 **Demo Marketing Scheme**\n\n**Campaign:** AUSHVERA Seasonal Wellness Drive\n\n**Offers:**\n- Buy 2 Get 1 Free on all herbal teas\n- 20% off orders above ₹999\n- Free ritual guide PDF with every order\n\n**Social Media:**\n1. 'Heal from within' herb photography series\n2. Customer testimonial reels\n3. Ayurvedic tip-of-the-day posts\n\n---\n*Add `OPENAI_API_KEY` to `.env` for AI-generated campaigns tailored to your prompt.*",
            'content' => "✍️ **Demo Content**\n\n**Product Description:**\nAUSHVERA Ashvattha™ Herbal Tea — A sacred blend of ancient botanicals, carefully sourced from the foothills of the Himalayas. Each sip is a ritual of renewal, harmonizing your body's natural rhythms with the wisdom of Ayurveda.\n\n**Tagline:** *Wellness, refined by nature.*\n\n---\n*Add `OPENAI_API_KEY` to `.env` for custom AI-generated content.*",
            default  => "👋 **AI Assistant Demo Mode**\n\nI'm ready to help with your AUSHVERA business! However, no AI API key is configured yet.\n\n**To enable real AI responses:**\n1. Get a free key at [aistudio.google.com](https://aistudio.google.com) (Gemini) or [platform.openai.com](https://platform.openai.com)\n2. Add to `.env`:\n   ```\n   GEMINI_API_KEY=your-key-here\n   ```\n   or\n   ```\n   OPENAI_API_KEY=sk-your-key-here\n   ```\n3. Run `php artisan config:clear`\n4. Start chatting!\n\n**Your question was:** {$message}",
        };
    }

    private function downloadAndSaveImage(string $url): ?string
    {
        try {
            $dir = public_path('uploads/ai-images');
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $filename = 'ai_' . Str::random(12) . '.png';
            $content  = file_get_contents($url);
            if ($content) {
                file_put_contents($dir . '/' . $filename, $content);
                return $dir . '/' . $filename;
            }
        } catch (\Exception $e) {
            // silent fail
        }
        return null;
    }

    private function logAndTrack(array $result, Request $request, $user, string $mode): void
    {
        try {
            AiLog::create([
                'prompt'         => $request->input('message', ''),
                'response'       => $result['content'] ?? (isset($result['image_url']) ? '[image generated]' : json_encode($result)),
                'mode'           => $mode,
                'input_tokens'   => $result['input_tokens'] ?? null,
                'output_tokens'  => $result['output_tokens'] ?? null,
                'estimated_cost' => $result['cost_estimate'] ?? null,
                'image_path'     => $result['saved_image_path'] ?? null,
            ]);
        } catch (\Exception $e) { /* silent */ }

        if ($user) {
            try {
                $user->increment('ai_used');
                if (!empty($result['input_tokens'])) {
                    $user->increment('monthly_tokens', ($result['input_tokens'] ?? 0) + ($result['output_tokens'] ?? 0));
                }
                if (!empty($result['cost_estimate'])) {
                    $user->increment('monthly_cost', $result['cost_estimate']);
                }
            } catch (\Exception $e) { /* silent */ }
        }
    }
}

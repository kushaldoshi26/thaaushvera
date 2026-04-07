<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;  // Anyone can submit contact form
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
            'email'   => ['required', 'email:rfc,dns', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    /**
     * Custom messages for validation errors
     */
    public function messages(): array
    {
        return [
            'name.required'    => 'Your name is required',
            'name.string'      => 'Your name must be a valid text string',
            'name.max'         => 'Your name cannot exceed 100 characters',
            'name.regex'       => 'Your name contains invalid characters',
            
            'email.required'   => 'Your email address is required',
            'email.email'      => 'Please provide a valid email address',
            'email.max'        => 'Your email cannot exceed 150 characters',
            
            'message.required' => 'Please type a message',
            'message.string'   => 'Your message must be text',
            'message.min'      => 'Your message must be at least 10 characters long',
            'message.max'      => 'Your message cannot exceed 2000 characters',
        ];
    }

    /**
     * Sanitize input data
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();
        
        // Sanitize inputs
        $validated['name'] = trim(htmlspecialchars($validated['name'], ENT_QUOTES, 'UTF-8'));
        $validated['email'] = trim(strtolower($validated['email']));
        $validated['message'] = trim(htmlspecialchars($validated['message'], ENT_QUOTES, 'UTF-8'));
        
        return $key ? ($validated[$key] ?? $default) : $validated;
    }
}

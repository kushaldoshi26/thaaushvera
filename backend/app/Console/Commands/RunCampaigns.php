<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // simple scheduler: mark due campaigns as sent (placeholder)
        $campaigns = \App\Models\AiCampaign::where('status', 'draft')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($campaigns as $campaign) {
            // in a real system we would dispatch API calls or emails
            $campaign->status = 'sent';
            $campaign->save();
            $this->info("Campaign {$campaign->id} sent (simulated).");
        }
    }
}

<?php

namespace App\Jobs;

use App\Models\UserDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExpireBadges
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Log::info("Started Badges expired for all vendor users ");
        // UserDetail::where('as_a', 'Vendor')->update(['badge' => '0']);
        // ExpireBadges::dispatch()->delay(now()->addSeconds(2));
        Log::info('Badges expired for all vendor users');
    }
}

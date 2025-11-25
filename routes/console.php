<?php

// use Illuminate\Foundation\Inspiring;
// use Illuminate\Support\Facades\Artisan;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

use App\Jobs\ExpireBadges;
use Illuminate\Support\Facades\Schedule;

// Schedule::call(function () {
//     logger('✅ Cron is working fine: '.now());
// })->everyMinute();

// Schedule the ExpiryJob to run every hour
Schedule::job(new ExpireBadges)->everyMinute();

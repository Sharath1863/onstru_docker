<?php
1

namespace App\Console;

use App\Jobs\ExpireBadges;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Schedule the ExpireBadges job to run monthly
        // Log::info('Scheduling ExpireBadges job!');
        $schedule->job(new ExpireBadges)->everyMinute();
        // ExpireBadges::dispatch()->delay(now()->addSeconds(2));
        // $schedule->job(new ExpireBadges)->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}

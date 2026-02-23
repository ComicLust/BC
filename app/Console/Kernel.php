<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * These cron jobs are run in the background and do not require user interaction.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $frequency = \App\Models\Setting::get('backlink_check_frequency', 'weekly');
        $command = $schedule->command('backlinks:check-all');

        if ($frequency === 'daily') {
            $command->daily();
        } elseif ($frequency === 'weekly') {
            $command->weekly();
        } elseif ($frequency === 'monthly') {
            $command->monthly();
        }
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
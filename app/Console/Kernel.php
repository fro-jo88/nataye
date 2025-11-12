<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [];

    protected function schedule(Schedule $schedule): void
    {
        // Process queued notifications
        $schedule->command('queue:work --stop-when-empty')
            ->everyFiveMinutes()
            ->withoutOverlapping();

        // Clean old audit logs (older than 90 days)
        $schedule->call(function () {
            \App\Models\AuditLog::where('created_at', '<', now()->subDays(90))->delete();
        })->daily();

        // Send daily attendance reminders
        $schedule->call(function () {
            // Implementation for attendance reminders
        })->dailyAt('08:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

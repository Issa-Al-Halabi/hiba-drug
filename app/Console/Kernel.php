<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        Commands\OrderNotifictionDelete::class,
      \App\Console\Commands\RequireSpatieTranslatable::class,
    ];


    protected function schedule(Schedule $schedule)
    {
        $schedule->command('OrderNotificationDelete:cron')->daily();
       $schedule->command('composer:require-spatie-translatable')->once();
    }


    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}


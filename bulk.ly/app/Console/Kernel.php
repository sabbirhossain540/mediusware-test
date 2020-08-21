<?php

namespace Bulkly\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
       \Bulkly\Console\Commands\SendPosts::class,
       \Bulkly\Console\Commands\RssAuto::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {


       $schedule->command('sendpost')->withoutOverlapping()->appendOutputTo('/home/bulk/public_html/app/public/amieami/'.date('j-F-Y-h-i-s-a').'-log.txt');
       $schedule->command('rss')->hourly()->appendOutputTo('/home/bulk/public_html/app/public/amieami/'.date('j-F-Y-h-i-s-a').'-rss-log.txt');





    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}

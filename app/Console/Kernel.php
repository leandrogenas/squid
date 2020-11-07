<?php

    namespace App\Console;

    use App\Models\feed\ListFeed;
    use App\Models\feed\ProcessFeeds;
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
            //
        ];

        /**
         * Define the application's command schedule.
         *
         * @param \Illuminate\Console\Scheduling\Schedule $schedule
         * @return void
         */
        protected function schedule(Schedule $schedule)
        {
            //php /home/iwinc771/web/syncweb.xyz/public_html/artisan schedule:run 1>> /dev/null 2>&1
            $schedule->call(function () {
                $processo = new ProcessFeeds();
                $processo->iniciar_processo();
            });
            $schedule->call(function () {
                ListFeed::limparLinksAntigo();
            })->monthly();
        }

        /**
         * Register the commands for the application.
         *
         * @return void
         */
        protected function commands()
        {
            $this->load(__DIR__ . '/Commands');

            require base_path('routes/console.php');
        }
    }

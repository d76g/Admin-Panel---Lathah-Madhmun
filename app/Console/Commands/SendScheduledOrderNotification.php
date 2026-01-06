<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendScheduledOrderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-scheduled-order-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes the scheduleNotification.js file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $node_path = env('NODE_PATH', '');

        if (! empty($node_path)) {

            // Run the JS file using Node.js
            $command = $node_path . ' --no-experimental-fetch --max-old-space-size=1024 ' . storage_path('app/firebase/scheduleNotification.js');
            \Log::info("Running command: " . $command);
            $output = shell_exec($command . " /dev/null 2>&1");

            \Log::info('Schedule notification Output: ' . $output);

            $this->info('Schedule notification process executed.');
        } else {

            // Log the output
            \Log::info('schedule notification Output: Node path is not defined');
        }
    }
}

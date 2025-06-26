<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class NotifyUpcomingTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-upcoming-tasks';

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
        $now = now();
        $upcomingTasks = Task::where('start_time', '<=', $now->addMinutes(10))
                            ->where('notified', false)
                            ->get();

        foreach ($upcomingTasks as $task) {
            Notification::create([
                'user_id' => $task->user_id,
                'title' => 'Task Reminder',
                'message' => "Your task '{$task->name}' starts soon.",
            ]);

            // (Optional) Push via Expo
            // Update task as notified
            $task->update(['notified' => true]);
        }
    }
}

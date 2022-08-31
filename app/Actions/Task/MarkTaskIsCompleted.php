<?php

namespace App\Actions\Task;

use App\Models\Task;
use Carbon\Carbon;

class MarkTaskIsCompleted
{
    public function handle(int $task_id)
    {
        try {
            $task = Task::query()->find($task_id);
            $task->is_completed = true;
            $task->submited_at = Carbon::now()->format('Y-m-d H:i:s');
            $task->save();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}

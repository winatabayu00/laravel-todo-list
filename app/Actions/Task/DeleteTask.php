<?php

namespace App\Actions\Task;

use App\Models\Task;
use InvalidArgumentException;

class DeleteTask
{
    public function handle(int $task_id)
    {
        try {
            /* find task */
            $task = Task::query()->where('id', '=', $task_id)->first();
            if (!$task) { // task not found
                throw new InvalidArgumentException('Cannot find the task!.');
            }

            $task->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

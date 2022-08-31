<?php

namespace App\Actions\AdditionalTask;

use App\Exceptions\TaskHandler;
use App\Models\AdditionalTask;
use App\Models\Task;

class DeleteAdditionalTask
{
    public function handle(int $additional_task_id)
    {
        try {
            $additional_task = AdditionalTask::query()->find($additional_task_id);
            if (!$additional_task) {
                throw TaskHandler::missing_additional_task();
            }

            /* cannot delete task when additional task was received by receiver */
            if ($additional_task->additional_task_status == AdditionalTask::status_accepted) {
                throw TaskHandler::task_already_accepted_by_receiver();
            }

            /* find task and deleted task also */
            $task = Task::query()->find($additional_task->task_id);
            if (!$task) { // task cannot be empty
                throw TaskHandler::missing_task();
            }
            $task->delete(); // cannot delete from this because task create from factory??? yess

            /* delete additional task */
            // $additional_task->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

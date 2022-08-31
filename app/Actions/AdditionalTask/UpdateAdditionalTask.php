<?php

namespace App\Actions\AdditionalTask;

use App\Exceptions\TaskHandler;
use App\Models\AdditionalTask;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class UpdateAdditionalTask
{
    public function handle(AdditionalTask $additional_task, array $input) // only updated by user who have role manager & boss (role level 1 & 2)
    {
        try {
            /* check if new receiver detected */
            if ($additional_task->receiver_id != $input['receiver_id']) {
                /* when additional task status is recieved */
                if ($additional_task->additional_task_status == AdditionalTask::status_accepted) { /* if status is recieved, cannot change to new receiver (if want to change new receiver, must create new task again) */
                    /* if additional task is canceled */
                    if ($input['additional_task_status'] != AdditionalTask::status_canceled) {
                        /* validate additional task format */
                        $validate_additional_task = Validator::make($additional_task->toArray(), [
                            'receiver' => ['required']
                        ]);

                        if ($validate_additional_task->fails()) {
                            throw TaskHandler::invalid_parameter();
                        }
                        throw TaskHandler::task_is_recived_by_user($additional_task->receiver->name);
                    }
                }
            }
            /* update new additional task */
            // $additional_task->task->group = $input['group'];
            $additional_task->task->title = $input['title'];
            $additional_task->task->description = $input['description'];
            $additional_task->task->due_date = Carbon::parse($input['due_date'])->format('Y-m-d H:i:s');
            $additional_task->receiver_id = $input['receiver_id'];
            // $additional_task->additional_task_status = $input['additional_task_status'];
            $additional_task->push();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

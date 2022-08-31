<?php

namespace App\Actions\Task;

use App\Models\Task;
use Carbon\Carbon;
use InvalidArgumentException;

class UpdateTask
{
    public function handle(int $task_id, array $input)
    {
        try {
            /* parse custom input */
            $parse_datetime_format = Carbon::createFromFormat('Y-m-d H:i', $input['due_date'])->format('Y-m-d H:i');
            $set_is_priority = (isset($input['set_as_priority']) && $input['set_as_priority'] == 'on' ? true : false);
            $input = array_merge([
                'due_date' => $parse_datetime_format,
                'is_priority' => $set_is_priority,
            ], $input);

            /* find the task */
            $task = Task::query()->where('id', '=', $task_id)->first();

            if (!$task) { // task not found
                throw new InvalidArgumentException('Cannot find the task!.');
            }

            /* update task */
            $task->update($input);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

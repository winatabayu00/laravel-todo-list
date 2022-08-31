<?php

namespace App\Actions\Task;

use App\Models\Task;
use Carbon\Carbon;

class NewTask
{
    public function handle($user, array $input)
    {

        try {
            /* parse custom input */
            $parse_datetime_format = Carbon::createFromFormat('Y-m-d H:i', $input['due_date'])->format('Y-m-d H:i');
            $set_is_priority = (isset($input['set_as_priority']) && $input['set_as_priority']  == 'on' ? true : false);
            $input = array_merge([
                'due_date' => $parse_datetime_format,
                'is_priority' => $set_is_priority,
            ], $input);

            /* create new task */
            $new_task = new Task($input);

            /* bind new task to user model */
            $user->my_tasks()->save($new_task);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

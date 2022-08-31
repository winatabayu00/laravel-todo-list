<?php

namespace App\Actions\AdditionalTask;

use App\Models\AdditionalTask;
use App\Models\Task;
use App\Models\User;

class NewAdditionalTask
{
    public function handle($user, array $input) // only created by user who have role manager & boss (role level 1 & 2)
    {
        /* create new task */
        $new_task = Task::query()->create($input);
        /* add task to additional task then send to receiver */
        $new_additional_task = new AdditionalTask($input);

        /* add who sending the additional task */
        $user = User::query()->find($user->id);
        $new_additional_task->sender()->associate($user);

        /* save task and new receiver */
        $new_task->additional_task()->save($new_additional_task);
    }
}

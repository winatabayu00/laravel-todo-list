<?php

namespace App\Actions\AdditionalTask;

use App\Models\AdditionalTask;

class DeclineAdditionaltask
{
    public function handle(int $additional_task_id, array $input)
    {
        $additional_task = AdditionalTask::query()->find($additional_task_id);
        $additional_task->notes = $input['notes'];
        $additional_task->save();
    }
}

<?php

namespace App\Actions\AdditionalTask;

use App\Exceptions\TaskHandler;
use App\Models\AdditionalTask;
use App\Models\Spatie\Role;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Exceptions\UnauthorizedException;

class ReceivedAdditionalTask
{
    public function handle($user, AdditionalTask $additionalTask, array $input)
    {
        try {

            switch ($input['additional_task_status']) {
                case AdditionalTask::status_accepted:

                    if ($user->hasRole(Role::role_boss)) {
                        throw UnauthorizedException::forRoles([Role::role_boss]);
                    }
                    /* for receiver id will check if contain with Logged in user */
                    if ($user->id != $additionalTask->receiver_id) {
                        throw TaskHandler::invalid_receiver();
                    }

                    /* ignore if additional task already canceled */
                    if ($additionalTask->additional_task_status == AdditionalTask::status_canceled) {
                        TaskHandler::task_already_canceled_by_sender();
                    }

                    /* change additional task status */
                    $additionalTask->additional_task_status = AdditionalTask::status_accepted;

                    /* get the receiver task */
                    $receiver = User::query()->find($additionalTask->receiver_id);
                    /* update task table for user_id reference by receiver_id */
                    $task = Task::query()->find($additionalTask->task_id);
                    $task->task_owner()->associate($receiver);
                    $task->save();
                    $additionalTask->save();
                    break;
                case AdditionalTask::status_decline:
                    if ($user->hasRole(Role::role_boss)) {
                        throw UnauthorizedException::forRoles([Role::role_boss]);
                    }
                    /* change additional task status */
                    $additionalTask->additional_task_status = AdditionalTask::status_decline;
                    /* add notes */
                    $additionalTask->notes = $input['notes'];
                    $additionalTask->save();
                    break;
                case AdditionalTask::status_pospone:
                    if ($user->hasRole(Role::role_boss)) {
                        throw UnauthorizedException::forRoles([Role::role_boss]);
                    }
                    /* change additional task status */
                    $additionalTask->additional_task_status = AdditionalTask::status_pospone;
                    /* add notes */
                    $additionalTask->notes = $input['notes'];
                    /* add pospone until (format datetime) */
                    $additionalTask->pospone_until = Carbon::parse($input['pospone_until']);
                    $additionalTask->save();
                    break;
                case AdditionalTask::status_canceled:
                    if ($user->hasRole(Role::role_employee)) {
                        throw UnauthorizedException::forRoles([Role::role_employee]);
                    }
                    /* change additional task status */
                    $additionalTask->additional_task_status = AdditionalTask::status_canceled;
                    /* add notes */
                    $additionalTask->notes = $input['notes'];
                    $additionalTask->save();
                    break;

                default:
                    # code...
                    break;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

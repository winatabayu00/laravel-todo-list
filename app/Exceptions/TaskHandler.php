<?php

namespace App\Exceptions;

use InvalidArgumentException;

class TaskHandler extends InvalidArgumentException
{
    public static function task_is_recived_by_user(string $name_user)
    {
        return new static("Task already recieved by `{$name_user}`.");
    }

    public static function invalid_parameter()
    {
        return new static('Invalid mandatory parameter!. ');
    }

    public static function missing_additional_task()
    {
        return new static('Additional task not found!.');
    }

    public static function missing_task()
    {
        return new static('Task not found!.');
    }

    public static function invalid_receiver()
    {
        return new static('You are not the one who accepted this task !.');
    }

    public static function task_already_accepted_by_receiver()
    {
        return new static('Cannot delete this task, because the task was accepted by user!. Change task status if want to deleted it!.');
    }

    public static function task_already_canceled_by_sender()
    {
        return new static('Cannot accept this task, because the task was canceled by sender!.');
    }
}

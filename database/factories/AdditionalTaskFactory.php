<?php

namespace Database\Factories;

use App\Models\Spatie\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdditionalTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user_sender = User::factory()->create()->assignRole(Role::role_manager);
        $user_receiver = User::factory()->create()->assignRole(Role::role_employee);
        $task = Task::factory()->create();
        return [
            'sender_id' => $user_sender->id,
            'receiver_id' => $user_receiver->id,
            'task_id' => $task->id,
        ];
    }
}

<?php

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

use App\Models\AdditionalTask;
use App\Models\Spatie\Role;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

/* store */

it('can\'t create status (unauthorize)', function () {
    $receiver = User::factory()->create()->assignRole(Role::role_employee);
    postJson(route('api.additional-task.store'), [
        'receiver_id' => $receiver->id,
        'group' => Str::random(10),
        'title' => Str::random(10),
        'description' => Str::random(10),
        'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
    ])->assertUnauthorized();
});


it('can\'t create additional task (unporcessable)', function (User $manager, User $boss) {
    actingAs($manager)
        ->postJson(route('api.additional-task.store'), [])->assertUnprocessable();
    actingAs($boss)
        ->postJson(route('api.additional-task.store'), [])->assertUnprocessable();
})->with('user_manager', 'user_boss');

it('can\'t create additional task (user is employee)', function (User $user, User $employee) {
    $receiver = User::factory()->create()->assignRole(Role::role_employee);
    actingAs($user)
        ->postJson(route('api.additional-task.store'), [
            'receiver_id' => $receiver->id,
            'group' => Str::random(10),
            'title' => Str::random(10),
            'description' => Str::random(10),
            'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
        ])->assertForbidden();
    actingAs($employee)
        ->postJson(route('api.additional-task.store'), [
            'receiver_id' => $receiver->id,
            'group' => Str::random(10),
            'title' => Str::random(10),
            'description' => Str::random(10),
            'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
        ])->assertForbidden();
})->with('user', 'user_employee');

it('can create additional task (user is manager && boss)', function (User $manager, User $boss) {
    $receiver = User::factory()->create()->assignRole(Role::role_employee);
    actingAs($manager)
        ->postJson(route('api.additional-task.store'), [
            'receiver_id' => $receiver->id,
            'group' => Str::random(10),
            'title' => Str::random(10),
            'description' => Str::random(10),
            'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
        ])->assertOk();
    actingAs($boss)
        ->postJson(route('api.additional-task.store'), [
            'receiver_id' => $receiver->id,
            'group' => Str::random(10),
            'title' => Str::random(10),
            'description' => Str::random(10),
            'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
        ])->assertOk();
})->with('user_manager', 'user_boss');



/* update */
it('can\'t update additional task (unauthorize)', function () {
    $receiver = User::factory()->create()->assignRole(Role::role_employee);
    $additional_task = AdditionalTask::factory()->create();
    putJson(route('api.additional-task.update', $additional_task->id), [
        'receiver_id' => $receiver->id,
        'group' => Str::random(10),
        'title' => Str::random(10),
        'description' => Str::random(10),
        'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
    ])->assertUnauthorized();
});


it('can\'t update additional task (unporcessable)', function (User $manager, User $boss) {
    $additional_task = AdditionalTask::factory()->create();
    actingAs($manager)
        ->putJson(route('api.additional-task.update', $additional_task->id), [])->assertUnprocessable();
    actingAs($boss)
        ->putJson(route('api.additional-task.update', $additional_task->id), [])->assertUnprocessable();
})->with('user_manager', 'user_boss');

it('can\'t update additional task (user is employee)', function (User $user, User $employee) {
    $receiver = User::factory()->create()->assignRole(Role::role_employee);
    $additional_task = AdditionalTask::factory()->create();
    actingAs($user)
        ->putJson(route('api.additional-task.update', $additional_task->id), [
            'receiver_id' => $receiver->id,
            'group' => Str::random(10),
            'title' => Str::random(10),
            'description' => Str::random(10),
            'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
        ])->assertForbidden();
    actingAs($employee)
        ->putJson(route('api.additional-task.update', $additional_task->id), [
            'receiver_id' => $receiver->id,
            'group' => Str::random(10),
            'title' => Str::random(10),
            'description' => Str::random(10),
            'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
        ])->assertForbidden();
})->with('user', 'user_employee');

it('can update additional task (user is manager && boss)', function (User $manager, User $boss) {
    $receiver = User::factory()->create()->assignRole(Role::role_employee);
    $additional_task = AdditionalTask::factory()->create();
    actingAs($manager)
        ->putJson(route('api.additional-task.update', $additional_task->id), [
            'receiver_id' => $receiver->id,
            'additional_task_status' => AdditionalTask::status_canceled,
            'group' => Str::random(10),
            'title' => Str::random(10),
            'description' => Str::random(10),
            'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
        ])->assertOk();
    actingAs($boss)
        ->putJson(route('api.additional-task.update', $additional_task->id), [
            'receiver_id' => $receiver->id,
            'additional_task_status' => AdditionalTask::status_canceled,
            'group' => Str::random(10),
            'title' => Str::random(10),
            'description' => Str::random(10),
            'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
        ])->assertOk();
})->with('user_manager', 'user_boss');


it('can\'t update additional task (because was accepted by receiver)', function (User $manager, User $boss) {
    $user_sender = User::factory()->create()->assignRole(Role::role_manager);
    $user_receiver = User::factory()->create()->assignRole(Role::role_employee);
    $task = Task::factory()->create();
    $receiver = User::factory()->create()->assignRole(Role::role_employee);
    $additional_task = AdditionalTask::factory()->create([
        'sender_id' => $user_sender->id,
        'receiver_id' => $user_receiver->id,
        'task_id' => $task->id,
        'additional_task_status' => AdditionalTask::status_accepted,
    ]);
    actingAs($manager)
        ->putJson(route('api.additional-task.update', $additional_task->id), [
            'receiver_id' => $receiver->id,
            'additional_task_status' => AdditionalTask::status_pending,
            'group' => Str::random(10),
            'title' => Str::random(10),
            'description' => Str::random(10),
            'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
        ])->assertStatus(500);
    $receiver = User::factory()->create()->assignRole(Role::role_employee);
    $additional_task = AdditionalTask::factory()->create([
        'sender_id' => $user_sender->id,
        'receiver_id' => $user_receiver->id,
        'task_id' => $task->id,
        'additional_task_status' => AdditionalTask::status_accepted,
    ]);
    actingAs($boss)
        ->putJson(route('api.additional-task.update', $additional_task->id), [
            'receiver_id' => $receiver->id,
            'additional_task_status' => AdditionalTask::status_pending,
            'group' => Str::random(10),
            'title' => Str::random(10),
            'description' => Str::random(10),
            'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
        ])->assertStatus(500);
})->with('user_manager', 'user_boss');

it('can cancel additional task (when task already accepted by receiver)', function (User $manager, User $boss) {
    $user_sender = User::factory()->create()->assignRole(Role::role_manager);
    $user_receiver = User::factory()->create()->assignRole(Role::role_employee);
    $task = Task::factory()->create();
    $receiver = User::factory()->create()->assignRole(Role::role_employee);
    $additional_task = AdditionalTask::factory()->create([
        'sender_id' => $user_sender->id,
        'receiver_id' => $user_receiver->id,
        'task_id' => $task->id,
        'additional_task_status' => AdditionalTask::status_accepted,
    ]);
    actingAs($manager)
        ->putJson(route('api.additional-task.update', $additional_task->id), [
            'receiver_id' => $receiver->id,
            'additional_task_status' => AdditionalTask::status_canceled,
            'group' => Str::random(10),
            'title' => Str::random(10),
            'description' => Str::random(10),
            'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
        ])->assertOk();
    $receiver = User::factory()->create()->assignRole(Role::role_employee);
    $additional_task = AdditionalTask::factory()->create([
        'sender_id' => $user_sender->id,
        'receiver_id' => $user_receiver->id,
        'task_id' => $task->id,
        'additional_task_status' => AdditionalTask::status_accepted,
    ]);
    actingAs($boss)
        ->putJson(route('api.additional-task.update', $additional_task->id), [
            'receiver_id' => $receiver->id,
            'additional_task_status' => AdditionalTask::status_canceled,
            'group' => Str::random(10),
            'title' => Str::random(10),
            'description' => Str::random(10),
            'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
        ])->assertOk();
})->with('user_manager', 'user_boss');

/* delete */

it('can\'t delete additional task (unauthorize)', function () {
    $user_sender = User::factory()->create()->assignRole(Role::role_manager);
    $user_receiver = User::factory()->create()->assignRole(Role::role_employee);
    $task = Task::factory()->create();
    $additional_task = AdditionalTask::factory()->create([
        'sender_id' => $user_sender->id,
        'receiver_id' => $user_receiver->id,
        'task_id' => $task->id,
    ]);
    deleteJson(route('api.additional-task.destroy', $additional_task->id))->assertUnauthorized();
});

it('can\'t delete additional task (user is employee)', function (User $user, User $employee) {
    $user_sender = User::factory()->create()->assignRole(Role::role_manager);
    $user_receiver = User::factory()->create()->assignRole(Role::role_employee);
    $task = Task::factory()->create();
    $additional_task = AdditionalTask::factory()->create([
        'sender_id' => $user_sender->id,
        'receiver_id' => $user_receiver->id,
        'task_id' => $task->id,
        'additional_task_status' => AdditionalTask::status_pending,
    ]);
    actingAs($user)
        ->deleteJson(route('api.additional-task.destroy', $additional_task->id))->assertForbidden();
    actingAs($employee)
        ->deleteJson(route('api.additional-task.destroy', $additional_task->id))->assertForbidden();
})->with('user', 'user_employee');

it('can delete additional task (user is manager && boss)', function (User $manager, User $boss) {
    $user_sender = User::factory()->create()->assignRole(Role::role_manager);
    $user_receiver = User::factory()->create()->assignRole(Role::role_employee);
    $task = Task::factory()->create();
    $additional_task = AdditionalTask::factory()->create([
        'sender_id' => $user_sender->id,
        'receiver_id' => $user_receiver->id,
        'task_id' => $task->id,
        'additional_task_status' => AdditionalTask::status_pending,
    ]);
    actingAs($manager)
        ->deleteJson(route('api.additional-task.destroy', $additional_task->id))->assertOk();
    $additional_task = AdditionalTask::factory()->create([
        'sender_id' => $user_sender->id,
        'receiver_id' => $user_receiver->id,
        'task_id' => $task->id,
        'additional_task_status' => AdditionalTask::status_pending,
    ]);
    actingAs($boss)
        ->deleteJson(route('api.additional-task.destroy', $additional_task->id))->assertOk();
})->with('user_manager', 'user_boss');

it('can\'t delete additional task (task was accepted by receiver)', function (User $manager, User $boss) {
    $user_sender = User::factory()->create()->assignRole(Role::role_manager);
    $user_receiver = User::factory()->create()->assignRole(Role::role_employee);
    $task = Task::factory()->create();
    $additional_task = AdditionalTask::factory()->create([
        'sender_id' => $user_sender->id,
        'receiver_id' => $user_receiver->id,
        'task_id' => $task->id,
        'additional_task_status' => AdditionalTask::status_accepted,
    ]);
    actingAs($manager)
        ->deleteJson(route('api.additional-task.destroy', $additional_task->id))->assertStatus(500);
    $additional_task = AdditionalTask::factory()->create([
        'sender_id' => $user_sender->id,
        'receiver_id' => $user_receiver->id,
        'task_id' => $task->id,
        'additional_task_status' => AdditionalTask::status_accepted,
    ]);
    actingAs($boss)
        ->deleteJson(route('api.additional-task.destroy', $additional_task->id))->assertStatus(500);
})->with('user_manager', 'user_boss');


/* update status additional task */
it('can\'t update additional task status (unauthorize)', function () {
    $receiver = User::factory()->create()->assignRole(Role::role_employee);
    $additional_task = AdditionalTask::factory()->create();
    putJson(route('api.additional-task.update.status', $additional_task->id), [
        'receiver_id' => $receiver->id,
    ])->assertUnauthorized();
});

it('can\'t update additional task status (unporcessable)', function (User $manager, User $boss, User $employee) {
    $additional_task = AdditionalTask::factory()->create();
    actingAs($manager)
        ->putJson(route('api.additional-task.update.status', $additional_task->id), [])->assertUnprocessable();
    actingAs($boss)
        ->putJson(route('api.additional-task.update.status', $additional_task->id), [])->assertUnprocessable();
    actingAs($employee)
        ->putJson(route('api.additional-task.update.status', $additional_task->id), [])->assertUnprocessable();
})->with('user_manager', 'user_boss', 'user_employee');

/* huft cape */
/* it('can accepted additional task status (user is manager & employee)', function (User $manager, User $employee) {
    $user_sender = User::factory()->create()->assignRole(Role::role_manager);
    $task = Task::factory()->create();
    $additional_task = AdditionalTask::factory()->create([
        'sender_id' => $user_sender->id,
        'receiver_id' => $manager->id,
        'task_id' => $task->id,
    ]);
    $additional_task = AdditionalTask::factory()->create();
    actingAs($manager)
        ->putJson(route('api.additional-task.update.status', $additional_task->id), [
            'additional_task_status' => AdditionalTask::status_accepted,
            'notes' => Str::random(10),
        ])->assertOk();
    $additional_task = AdditionalTask::factory()->create([
        'sender_id' => $user_sender->id,
        'receiver_id' => $employee->id,
        'task_id' => $task->id,
    ]);
    actingAs($employee)
        ->putJson(route('api.additional-task.update.status', $additional_task->id), [
            'additional_task_status' => AdditionalTask::status_accepted,
            'notes' => Str::random(10),
        ])->assertOk();
})->with('user_manager', 'user_employee');

it('can\'t accepted additional task status (user is boss)', function (User $boss) {
    $user_sender = User::factory()->create()->assignRole(Role::role_manager);
    $task = Task::factory()->create();
    $additional_task = AdditionalTask::factory()->create([
        'sender_id' => $user_sender->id,
        'receiver_id' => $boss->id,
        'task_id' => $task->id,
        'additional_task_status' => AdditionalTask::status_accepted,
    ]);
    actingAs($boss)
        ->putJson(route('api.additional-task.update.status', $additional_task->id), [
            'additional_task_status' => AdditionalTask::status_accepted,
            'notes' => Str::random(10),
        ])->assertForbidden();
})->with('user_boss'); */

it('can cancel additional task status (user is manager & boss)', function (User $manager, User $boss) {
    $additional_task = AdditionalTask::factory()->create();
    actingAs($manager)
        ->putJson(route('api.additional-task.update.status', $additional_task->id), [
            'additional_task_status' => AdditionalTask::status_canceled,
            'notes' => Str::random(10),
        ])->assertOk();
    actingAs($boss)
        ->putJson(route('api.additional-task.update.status', $additional_task->id), [
            'additional_task_status' => Str::random(10),
            'notes' => AdditionalTask::status_canceled,
        ])->assertOk();
})->with('user_manager', 'user_boss');

it('can\'t cancel additional task status (user is employee)', function (User $employee) {
    $additional_task = AdditionalTask::factory()->create();
    actingAs($employee)
        ->putJson(route('api.additional-task.update.status', $additional_task->id), [
            'additional_task_status' => AdditionalTask::status_canceled,
            'notes' => Str::random(10),
        ])->assertForbidden();
})->with('user_employee');


// it('can\'t update additional task status (because was accepted by receiver)', function (User $manager, User $boss) {
//     $user_sender = User::factory()->create()->assignRole(Role::role_manager);
//     $user_receiver = User::factory()->create()->assignRole(Role::role_employee);
//     $task = Task::factory()->create();
//     $receiver = User::factory()->create()->assignRole(Role::role_employee);
//     $additional_task = AdditionalTask::factory()->create([
//         'sender_id' => $user_sender->id,
//         'receiver_id' => $user_receiver->id,
//         'task_id' => $task->id,
//         'additional_task_status' => AdditionalTask::status_accepted,
//     ]);
//     actingAs($manager)
//         ->putJson(route('api.additional-task.update.status', $additional_task->id), [
//             'receiver_id' => $receiver->id,
//             'additional_task_status' => AdditionalTask::status_pending,
//         ])->assertStatus(500);
//     $receiver = User::factory()->create()->assignRole(Role::role_employee);
//     $additional_task = AdditionalTask::factory()->create([
//         'sender_id' => $user_sender->id,
//         'receiver_id' => $user_receiver->id,
//         'task_id' => $task->id,
//         'additional_task_status' => AdditionalTask::status_accepted,
//     ]);
//     actingAs($boss)
//         ->putJson(route('api.additional-task.update.status', $additional_task->id), [
//             'receiver_id' => $receiver->id,
//             'additional_task_status' => AdditionalTask::status_pending,
//         ])->assertStatus(500);
// })->with('user_manager', 'user_boss');

// it('can cancel additional task status (when task already accepted by receiver)', function (User $manager, User $boss) {
//     $user_sender = User::factory()->create()->assignRole(Role::role_manager);
//     $user_receiver = User::factory()->create()->assignRole(Role::role_employee);
//     $task = Task::factory()->create();
//     $receiver = User::factory()->create()->assignRole(Role::role_employee);
//     $additional_task = AdditionalTask::factory()->create([
//         'sender_id' => $user_sender->id,
//         'receiver_id' => $user_receiver->id,
//         'task_id' => $task->id,
//         'additional_task_status' => AdditionalTask::status_accepted,
//     ]);
//     actingAs($manager)
//         ->putJson(route('api.additional-task.update.status', $additional_task->id), [
//             'receiver_id' => $receiver->id,
//             'additional_task_status' => AdditionalTask::status_canceled,
//         ])->assertOk();
//     $receiver = User::factory()->create()->assignRole(Role::role_employee);
//     $additional_task = AdditionalTask::factory()->create([
//         'sender_id' => $user_sender->id,
//         'receiver_id' => $user_receiver->id,
//         'task_id' => $task->id,
//         'additional_task_status' => AdditionalTask::status_accepted,
//     ]);
//     actingAs($boss)
//         ->putJson(route('api.additional-task.update.status', $additional_task->id), [
//             'receiver_id' => $receiver->id,
//             'additional_task_status' => AdditionalTask::status_canceled,
//         ])->assertOk();
// })->with('user_manager', 'user_boss');

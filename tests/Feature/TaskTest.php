<?php

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

use Illuminate\Support\Str;

/* store */

it('can\'t create task (unauthorize)', function () {
    postJson(route('api.task.store'), [
        'group' => Str::random(10),
        'title' => Str::random(10),
        'description' => Str::random(10),
        'due_date' => Carbon::now()->addHour(2),
    ])->assertUnauthorized();
});
it('can\'t create task (unprocessable)', function (User $user, User $boss, User $manager, User $employee) {
    actingAs($user)->postJson(route('api.task.store'), [])->assertUnprocessable();
    actingAs($boss)->postJson(route('api.task.store'), [])->assertUnprocessable();
    actingAs($manager)->postJson(route('api.task.store'), [])->assertUnprocessable();
    actingAs($employee)->postJson(route('api.task.store'), [])->assertUnprocessable();
})->with('user', 'user_boss', 'user_manager', 'user_employee');

it('can create task ', function (User $user, User $boss, User $manager, User $employee) {
    actingAs($user)->postJson(route('api.task.store'), [
        'group' => Str::random(10),
        'title' => Str::random(10),
        'description' => Str::random(10),
        'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
    ])->assertCreated();

    actingAs($boss)->postJson(route('api.task.store'), [
        'group' => Str::random(10),
        'title' => Str::random(10),
        'description' => Str::random(10),
        'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
    ])->assertCreated();

    actingAs($manager)->postJson(route('api.task.store'), [
        'group' => Str::random(10),
        'title' => Str::random(10),
        'description' => Str::random(10),
        'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
    ])->assertCreated();

    actingAs($employee)->postJson(route('api.task.store'), [
        'group' => Str::random(10),
        'title' => Str::random(10),
        'description' => Str::random(10),
        'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
    ])->assertCreated();
})->with('user', 'user_boss', 'user_manager', 'user_employee');


/* update */
it('can\'t update task (unauthorize)', function () {
    putJson(route('api.task.update', 1), [
        'group' => Str::random(10),
        'title' => Str::random(10),
        'description' => Str::random(10),
        'due_date' => Carbon::now()->addHour(2),
    ])->assertUnauthorized();
});
it('can\'t update task (unprocessable)', function (User $user, User $boss, User $manager, User $employee) {
    actingAs($user)->putJson(route('api.task.update', 1), [])->assertUnprocessable();
    actingAs($boss)->putJson(route('api.task.update', 1), [])->assertUnprocessable();
    actingAs($manager)->putJson(route('api.task.update', 1), [])->assertUnprocessable();
    actingAs($employee)->putJson(route('api.task.update', 1), [])->assertUnprocessable();
})->with('user', 'user_boss', 'user_manager', 'user_employee');
it('can update task ', function (User $user, User $boss, User $manager, User $employee) {
    $task = Task::factory()->create();
    $update_task = [
        'group' => Str::random(10),
        'title' => Str::random(10),
        'description' => Str::random(10),
        'due_date' => Carbon::now()->addHour(2)->format('Y-m-d H:i:s'),
    ];
    actingAs($user)->putJson(route('api.task.update', $task->id), $update_task)->assertOK();

    actingAs($boss)->putJson(route('api.task.update', $task->id), $update_task)->assertOK();

    actingAs($manager)->putJson(route('api.task.update', $task->id), $update_task)->assertOK();

    $update_task = array_merge($update_task, ['is_priority' => true]);
    actingAs($employee)->putJson(route('api.task.update', $task->id), $update_task)->assertOK();
})->with('user', 'user_boss', 'user_manager', 'user_employee');

/* delete */
it('can\'t delete task (unauthorize)', function () {
    deleteJson(route('api.task.destroy', 1), [
        'group' => Str::random(10),
        'title' => Str::random(10),
        'description' => Str::random(10),
        'due_date' => Carbon::now()->addHour(2),
    ])->assertUnauthorized();
});

it('can delete task ', function (User $user, User $boss, User $manager, User $employee) {
    $task = Task::factory()->create();
    actingAs($user)->deleteJson(route('api.task.destroy', $task->id))->assertOK();

    $task = Task::factory()->create();
    actingAs($boss)->deleteJson(route('api.task.destroy', $task->id))->assertOK();

    $task = Task::factory()->create();
    actingAs($manager)->deleteJson(route('api.task.destroy', $task->id))->assertOK();

    $task = Task::factory()->create();
    actingAs($employee)->deleteJson(route('api.task.destroy', $task->id))->assertOK();
})->with('user', 'user_boss', 'user_manager', 'user_employee');


/* task mark is compleyed */
it('can\'t mark task (unauthorize)', function () {
    putJson(route('api.task.mark-as-completed', 1), [
        'group' => Str::random(10),
        'title' => Str::random(10),
        'description' => Str::random(10),
        'due_date' => Carbon::now()->addHour(2),
    ])->assertUnauthorized();
});

it('can mark task ', function (User $user, User $boss, User $manager, User $employee) {
    $task = Task::factory()->create();
    actingAs($user)->putJson(route('api.task.mark-as-completed', $task->id))->assertOK();

    $task = Task::factory()->create();
    actingAs($boss)->putJson(route('api.task.mark-as-completed', $task->id))->assertOK();

    $task = Task::factory()->create();
    actingAs($manager)->putJson(route('api.task.mark-as-completed', $task->id))->assertOK();

    $task = Task::factory()->create();
    actingAs($employee)->putJson(route('api.task.mark-as-completed', $task->id))->assertOK();
})->with('user', 'user_boss', 'user_manager', 'user_employee');

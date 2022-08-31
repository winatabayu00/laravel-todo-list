<?php

use App\Http\Controllers\AdditionalTaskController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    /* Task */
    Route::post('/task', [TaskController::class, 'store'])->name('task.store');
    Route::put('/task/{task}', [TaskController::class, 'update'])->name('task.update');
    Route::delete('/task/{task}', [TaskController::class, 'destroy'])->name('task.destroy');
    Route::put('/task/{task}/marked-as-completed', [TaskController::class, 'mark_task_is_completed'])->name('task.mark-as-completed');

    /* Additional Task */
    Route::post('/additional-task', [AdditionalTaskController::class, 'store'])->name('additional-task.store')
        ->middleware(['role:Boss|Manager']);
    Route::put('/additional-task/{additional_task}', [AdditionalTaskController::class, 'update'])->name('additional-task.update')
        ->middleware(['role:Boss|Manager']);
    Route::delete('/additional-task/{additional_task}', [AdditionalTaskController::class, 'destroy'])->name('additional-task.destroy')
        ->middleware(['role:Boss|Manager']);
    Route::put('/additional-task/{additional_task}/status', [AdditionalTaskController::class, 'update_additional_task'])->name('additional-task.update.status')
        ->middleware(['role:Boss|Manager|Employee']);
});

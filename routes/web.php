<?php

use App\Http\Controllers\AdditionalTaskController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authentication'])->name('login.create');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    /* task */
    Route::post('/task-datatable', [TaskController::class, 'datatable'])->name('task.datatable');
    Route::resource('task', TaskController::class);
    Route::put('/task/{task}/marked-as-completed', [TaskController::class, 'mark_task_is_completed'])->name('task.mark-as-completed');

    Route::post('/additional-task-datatable', [AdditionalTaskController::class, 'datatable'])->name('additional-task.datatable');
    Route::resource('additional-task', AdditionalTaskController::class);
    Route::put('/additional-task/{additional_task}/status', [AdditionalTaskController::class, 'update_additional_task'])->name('additional-task.update.status')
        ->middleware(['role:Boss|Manager|Employee']);

    /* Logout */
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

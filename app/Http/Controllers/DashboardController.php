<?php

namespace App\Http\Controllers;

use App\Models\AdditionalTask;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $incoming_tasks = AdditionalTask::query()
            ->with(['task', 'sender'])
            ->where('additional_task_status', '=', AdditionalTask::status_pending)
            ->where('receiver_id', '=', $user->id)->get();

        $my_tasks = Task::query()
            ->whereDate('created_at', Carbon::now())
            ->where('is_completed', false)
            ->where('user_id', '=', $user->id)->get();

        $finish_task = Task::query()
            ->whereDate('created_at', Carbon::now())
            ->where('is_completed', true)
            ->where('user_id', '=', $user->id)->count();
        return view('pages.dashboard-index', [
            'my_tasks' => $my_tasks,
            'incoming_tasks' => $incoming_tasks,
            'finish_task' => $finish_task,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Actions\Task\DeleteTask;
use App\Actions\Task\MarkTaskIsCompleted;
use App\Actions\Task\NewTask;
use App\Actions\Task\UpdateTask;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\Models\Task;
use App\Traits\HasCustomResponse;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TaskController extends Controller
{
    use HasCustomResponse;

    protected $model_name = 'Task';

    public function datatable()
    {
        try {
            if (request()->ajax()) {
                $datatable = Task::query()
                    ->when(request()->get('period') == 'Today', function ($q) {
                        $q->whereDate('created_at', Carbon::now());
                    })->when(request()->get('period') == 'Yesterday', function ($q) {
                        $q->whereDate('created_at', Carbon::now()->addDay(-1));
                    })->when(request()->get('period') == 'Last 7-Days', function ($q) {
                        $q->whereBetween('created_at', [Carbon::now()->addDay(-8), Carbon::now()]);
                    })->when(request()->get('period') == 'Next 7-Days', function ($q) {
                        $q->whereBetween('created_at', [Carbon::now()->addDay(-1), Carbon::now()->addDay(7)]);
                    })
                    ->when(request()->get('filter_by') == 'Only Priority', function ($q) {
                        $q->where('is_priority', true);
                    })
                    ->whereDoesntHave('additional_task')
                    ->orderBy('is_completed', 'ASC')
                    ->orderBy('is_priority', 'DESC')
                    ->orderBy('updated_at', 'DESC')
                    ->get();

                return datatables()->of($datatable)
                    ->addIndexColumn()
                    ->addColumn('task_status', function ($row) {
                        return ($row->is_completed ? 'Completed' : 'In-Progress');
                    })
                    ->addColumn('actions', function ($row) {

                        $action = '<div class="d-inline-block text-nowrap dropright dropdown">';
                        $action .= '<button class="btn btn-sm btn-icon me-2 set-is-completed" data-id="' . $row->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark as Completed"><i class="fa-solid fa-circle-check"></i></button>';
                        $action .= '<button class="btn btn-sm btn-icon me-2 edit-record" data-id="' . $row->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Task"><i class="fa-solid fa-pen-to-square"></i></button>';
                        $action .=
                            '<button class="btn btn-sm btn-icon dropdown-menu-item" id="dropdown-menu-item" role="button" data-toggle="dropdown">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>';
                        $action .=
                            '<div class="dropdown-menu dropdown-menu-right">
                               <a href="javascript:;" class="dropdown-item detail-record" data-id="' . $row->id . '">Detail Record</a>
                               <a href="javascript:;" class="dropdown-item delete-record" data-id="' . $row->id . '">Delete</a>
                            </div>';

                        $action .= '</div>';
                        // $action .= '<button class="btn btn-sm btn-icon me-2 edit-record" data-id="' . $row->id . '"><i class="bx bx-edit"></i></button>';
                        return $action;
                    })
                    // ->addColumn('actions', function ($row) {

                    //     return $action;
                    // })
                    ->rawColumns(['actions'])
                    ->make(true);
            } else {
                throw new InvalidArgumentException('Request type is invalid!.');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $filters = ['Only Priority'];
        $periods = ['Today', 'Yesterday', 'Last 7-Days', 'Next 7-Days'];
        $task_statuses = ['Completed', 'In-Progress'];
        return view('pages.task.task-index', [
            'filters' => $filters,
            'periods' => $periods,
            'task_statuses' => $task_statuses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (request()->ajax()) {
            return response()->json([
                'view' => view('pages.task.modal.modal-create-task', [])->render()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskStoreRequest $request, NewTask $newTask)
    {
        $validator = $request->toArray();

        DB::beginTransaction();
        try {
            $user = Auth::user();
            if (!$user) {
                throw new AuthorizationException('User not logged in');
            }

            $newTask->handle($user, $validator);

            DB::commit();
            return response()->json(
                $this->success_create($this->model_name),
                201
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (request()->ajax()) {
            $task = Task::query()
                ->where('id', '=', $id)
                ->with(['additional_task.sender'])
                ->first();
            return response()->json([
                'view' => view('pages.task.modal.modal-show-task', [
                    'task_status' => ($task->is_completed ? 'Completed' : 'In-Progress'),
                    'creator' => (isset($task->additional_task->sender) ? $task->additional_task->sender->name : 'You'),
                    'created_at' => $task->created_at,
                    'task_title' => $task->title,
                    'task_description' => $task->description,
                    'finished_at' => $task->submited_at,
                ])->render()
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (request()->ajax()) {
            $task = Task::query()->find($id);
            return response()->json([
                'view' => view('pages.task.modal.modal-edit-task', [
                    'task' => $task,
                ])->render(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TaskUpdateRequest $request, $id, UpdateTask $updateTask)
    {
        $validator = $request->toArray();
        try {
            $user = Auth::user();
            if (!$user) {
                throw new AuthorizationException('User not logged in');
            }

            $updateTask->handle($id, $validator);
            return response()->json(
                $this->success_update($this->model_name)
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, DeleteTask $deleteTask)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new AuthorizationException('User not logged in');
            }

            $deleteTask->handle($id);
            return response()->json(
                $this->success_delete($this->model_name)
            );
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function mark_task_is_completed($task_id, MarkTaskIsCompleted $markTaskIsCompleted)
    {
        try {
            $markTaskIsCompleted->handle($task_id);
            return response()->json(
                $this->success_custom($this->model_name, 'marked as completed task!.'),
                200
            );
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

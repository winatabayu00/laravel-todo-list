<?php

namespace App\Http\Controllers;

use App\Actions\AdditionalTask\DeleteAdditionalTask;
use App\Actions\AdditionalTask\NewAdditionalTask;
use App\Actions\AdditionalTask\ReceivedAdditionalTask;
use App\Actions\AdditionalTask\UpdateAdditionalTask;
use App\Http\Requests\AdditionalTask\AdditionalTaskStatusRequest;
use App\Http\Requests\AdditionalTask\AdditionalTaskStoreRequest;
use App\Http\Requests\AdditionalTask\AdditionalTaskUpdateRequest;
use App\Models\AdditionalTask;
use App\Models\Spatie\Role;
use App\Models\User;
use App\Traits\HasCustomResponse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AdditionalTaskController extends Controller
{

    use HasCustomResponse;
    protected $model_name = 'Additional Task';

    public function datatable()
    {
        try {
            if (request()->ajax()) {
                $datatable = AdditionalTask::query()
                    ->with(['receiver', 'task'])
                    ->get();

                return datatables()->of($datatable)
                    ->addIndexColumn()

                    ->addColumn('receiver', function ($row) {
                        $receiver_name = '-';
                        if (isset($row->receiver) && $row->receiver !== null) {
                            $receiver_name = $row->receiver->name;
                        }
                        return $receiver_name;
                    })->addColumn('task_title', function ($row) {
                        $task_title = '-';
                        if (isset($row->task) && $row->task !== null) {
                            $task_title = $row->task->title;
                        }
                        return $task_title;
                    })->addColumn('task_due_date', function ($row) {
                        $task_title = '-';
                        if (isset($row->task) && $row->task !== null) {
                            $task_title = Carbon::parse($row->task->due_date)->format('d M Y, H:i');
                        }
                        return $task_title;
                    })->addColumn('task_status', function ($row) {
                        $task_status = '-';
                        if (isset($row->additional_task_status) && $row->additional_task_status !== null) {
                            $task_status = $row->additional_task_status;
                            if ($task_status == AdditionalTask::status_accepted) {
                                $task_status = ($row->task->is_completed ? 'Completed' : 'In-Progress');
                            }
                        }
                        return $task_status;
                    })
                    ->addColumn('actions', function ($row) {

                        $action = '<div class="d-inline-block text-nowrap dropright dropdown">';
                        if ($row->additional_task_status != AdditionalTask::status_canceled) {
                            $action .= '<button class="btn btn-sm btn-icon me-2 cancel-sending" data-id="' . $row->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Cancel Sending"><i class="fa-solid fa-xmark"></i></button>';
                        }
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
        return view('pages.task.additional-task.additional-task-index', []);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        if (request()->ajax()) {
            $assign_to_users = [];
            if (!$user->hasRole(Role::role_employee)) {
                $assign_to_users = User::query()
                    ->where('id', '!=', $user->id)
                    ->when($user->hasRole(Role::role_manager), function ($q) {
                        $q->whereHas('roles', function (Builder $search_by_roles) {
                            $search_by_roles->where('name', '!=', 'Boss');
                        });
                    })
                    ->get();
            }
            return response()->json([
                'view' => view('pages.task.additional-task.modal.modal-create-additional-task', [
                    'assign_to_users' => $assign_to_users,
                ])->render()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdditionalTaskStoreRequest $request, NewAdditionalTask $newAdditionalTask)
    {
        $validator = $request->toArray();

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $newAdditionalTask->handle($user, $validator);
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
            $additional_task = AdditionalTask::query()
                ->with(['task', 'receiver'])
                ->where('id', '=', $id)
                ->first();

            $task_status = 'Waiting to Accepted';
            if ($additional_task->additional_task_status == AdditionalTask::status_accepted) {
                $task_status = ($additional_task->task->is_completed ? 'Completed' : 'In-Progress');
            }

            return response()->json([
                'view' => view('pages.task.additional-task.modal.modal-show-additional-task', [
                    'task_status' => $task_status,
                    'receiver' => $additional_task->receiver->name,
                    'created_at' => $additional_task->created_at,
                    'task_title' => $additional_task->task->title,
                    'task_description' => $additional_task->task->description,
                    'finished_at' => $additional_task->task->submited_at,
                ])->render(),
            ]);
        }
        return;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        if (request()->ajax()) {
            $assign_to_users = [];
            if (!$user->hasRole(Role::role_employee)) {
                $assign_to_users = User::query()
                    ->where('id', '!=', $user->id)
                    ->when($user->hasRole(Role::role_manager), function ($q) {
                        $q->whereHas('roles', function (Builder $search_by_roles) {
                            $search_by_roles->where('name', '!=', 'Boss');
                        });
                    })
                    ->get();
            }
            $additional_task = AdditionalTask::query()
                ->with(['task'])
                ->where('id', '=', $id)->first();
            return response()->json([
                'view' => view('pages.task.additional-task.modal.modal-edit-additional-task', [
                    'assign_to_users' => $assign_to_users,
                    'additional_task' => $additional_task,
                    'task' => $additional_task->task,
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
    public function update(AdditionalTaskUpdateRequest $request, $id, UpdateAdditionalTask $updateAdditionalTask)
    {
        $validator = $request->toArray();

        DB::beginTransaction();
        try {
            $additional_task = AdditionalTask::query()
                ->with(['receiver']) // mandatory parameter
                ->where('id', '=', $id)->first();
            $updateAdditionalTask->handle($additional_task, $validator);
            DB::commit();
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
    public function destroy($id, DeleteAdditionalTask $deleteAdditionalTask)
    {
        DB::beginTransaction();
        try {
            $deleteAdditionalTask->handle($id);
            DB::commit();
            return response()->json(
                $this->success_delete($this->model_name)
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /* only function for handle updating status for additional task */
    public function update_additional_task(AdditionalTaskStatusRequest $request, $additional_task_id, ReceivedAdditionalTask $receivedAdditionalTask)
    {
        $validator = $request->toArray();
        try {
            $user = Auth::user();
            $additional_task = AdditionalTask::query()->find($additional_task_id);
            $receivedAdditionalTask->handle($user, $additional_task, $validator);
            return response()->json(
                $this->success_custom($this->model_name, 'was ' . request()->get('additional_task_status') . '!.'),
                200
            );
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /* public function decline_additional_task(AdditionalTaskDeclineRequest $request, $additional_task_id, DeclineAdditionaltask $declineAdditionaltask)
    {
        $validator = $request->toArray();
        try {
            $declineAdditionaltask->handle($additional_task_id, $validator);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pospone_additional_task(AdditionalTaskDeclineRequest $request, $additional_task_id, DeclineAdditionaltask $declineAdditionaltask)
    {
        $validator = $request->toArray();
        try {
            $declineAdditionaltask->handle($additional_task_id, $validator);
        } catch (\Throwable $th) {
            throw $th;
        }
    } */
}

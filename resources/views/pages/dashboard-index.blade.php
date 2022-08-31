@component('layouts.app')
    @section('page-title', 'Dashboard')
    @slot('page_body')
        <section class="section mb-3">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="far fa-newspaper"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Today Task</h4>
                            </div>
                            <div class="card-body">
                                {{ $my_tasks->count() }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="far fa-newspaper"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Finish Task</h4>
                            </div>
                            <div class="card-body">
                                {{ $finish_task }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="far fa-file"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Incoming Task</h4>
                            </div>
                            <div class="card-body">
                                {{ $incoming_tasks->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="d-inline">My Tasks</h4>
                            <div class="card-header-action">
                                <a href="{{ route('task.index') }}" class="btn btn-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled list-unstyled-border" style="overflow-y: scroll;height: 320px;">
                                @if (isset($my_tasks) && $my_tasks->count() > 0)
                                    @foreach ($my_tasks as $task)
                                        <li class="media">
                                            <div class="media-body">
                                                <div class="badge badge-pill badge-success mb-1 float-right"
                                                    id="mark-task-completed" type="button" data-id="{{ $task->id }}">
                                                    Mark as Completed</div>
                                                <h6 class="media-title"><a href="#">{{ $task->title }}</a></h6>
                                                <div class="text-small text-muted">{{ $task->description }}<div class="bullet">
                                                    </div>
                                                    <span
                                                        class="text-primary">{{ \Carbon\Carbon::parse($task->due_date)->format('d M, H:i') }}</span>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="d-inline">Incoming Tasks</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled list-unstyled-border" style="overflow-y: scroll;height: 320px;">
                                @if (isset($incoming_tasks) && $incoming_tasks->count() > 0)
                                    @foreach ($incoming_tasks as $task)
                                        <li class="media">
                                            <div class="media-body">

                                                <div class="badge badge-pill badge-danger mb-1 float-right" id="decline-task"
                                                    type="button" data-id="{{ $task->id }}">
                                                    Decline
                                                </div>
                                                <div class="badge badge-pill badge-success mb-1 float-right" id="accepted-task"
                                                    type="button" data-id="{{ $task->id }}">
                                                    Accepted
                                                </div>
                                                <h6 class="media-title"><a href="#">{{ $task->task->title }}</a></h6>
                                                <div class="text-small text-muted">{{ $task->task->description }}<div
                                                        class="bullet">
                                                    </div>
                                                    <span class="text-primary">{{ $task->sender->name }}</span>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endslot

@section('page-scripts')

    <script>
        let mark_completed_url = "{{ route('task.mark-as-completed', ':id') }}";
        let update_status_url = "{{ route('additional-task.update.status', ':id') }}";
        let csrf_token = "{{ csrf_token() }}";
    </script>
    <script>
        $(document).ready(function() {
            $('#mark-task-completed').click(function(e) {
                e.preventDefault();
                var id = $(this).attr("data-id");
                let mark_completed = mark_completed_url;
                mark_completed = mark_completed.replace(":id", id);
                $.ajax({
                    data: {
                        '_token': csrf_token,
                    },
                    url: mark_completed,
                    type: "PUT",
                    dataType: "json",
                    success: function(data) {
                        Swal.fire({
                            timer: 3000,
                            timerProgressBar: true,
                            icon: data.icon,
                            title: data.title,
                            text: data.message,
                            footer: "Alert will close automatically!.",
                        });
                        location.reload().draw();
                    },
                    error: function(error) {
                        console.log(error);
                        Swal.fire({
                            icon: error.responseJSON.icon,
                            title: error.responseJSON.title,
                            text: error.responseJSON
                                .message,
                            footer: '<a href="">Error Code: ' +
                                error.status +
                                ", " +
                                error.statusText +
                                "...</a>",
                        });
                    },
                });
            });
            $('#accepted-task').click(function(e) {
                e.preventDefault();
                var id = $(this).attr("data-id");
                let url_update_status = update_status_url;
                url_update_status = url_update_status.replace(":id", id);
                $.ajax({
                    data: {
                        '_token': csrf_token,
                        'additional_task_status': 'Accepted',
                    },
                    url: url_update_status,
                    type: "PUT",
                    dataType: "json",
                    success: function(data) {
                        Swal.fire({
                            timer: 3000,
                            timerProgressBar: true,
                            icon: data.icon,
                            title: data.title,
                            text: data.message,
                            footer: "Alert will close automatically!.",
                        });
                        location.reload().draw();
                    },
                    error: function(error) {
                        console.log(error);
                        Swal.fire({
                            icon: error.responseJSON.icon,
                            title: error.responseJSON.title,
                            text: error.responseJSON
                                .message,
                            footer: '<a href="">Error Code: ' +
                                error.status +
                                ", " +
                                error.statusText +
                                "...</a>",
                        });
                    },
                });
            });
            $('#decline-task').click(function(e) {
                e.preventDefault();
                var id = $(this).attr("data-id");
                input_notes();
                async function input_notes() {
                    const {
                        value: text_notes
                    } = await Swal.fire({
                        input: 'textarea',
                        inputLabel: 'Add Notes',
                        inputPlaceholder: 'Type your message here...',
                        inputAttributes: {
                            'aria-label': 'Type your message here'
                        },
                        showCancelButton: true
                    })

                    if (text_notes) {
                        Swal.fire({
                            title: "Are You sure?",
                            text: "You won't be able to revert this!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Confirm!",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let url_update_status = update_status_url;
                                url_update_status = url_update_status.replace(":id", id);
                                $.ajax({
                                    data: {
                                        '_token': csrf_token,
                                        'additional_task_status': 'Decline',
                                        'notes': text_notes,
                                    },
                                    url: url_update_status,
                                    type: "PUT",
                                    dataType: "json",
                                    success: function(data) {
                                        Swal.fire({
                                            timer: 3000,
                                            timerProgressBar: true,
                                            icon: data.icon,
                                            title: data.title,
                                            text: data.message,
                                            footer: "Alert will close automatically!.",
                                        });
                                        location.reload().draw();
                                    },
                                    error: function(error) {
                                        console.log(error);
                                        Swal.fire({
                                            icon: error.responseJSON.icon,
                                            title: error.responseJSON.title,
                                            text: error.responseJSON
                                                .message,
                                            footer: '<a href="">Error Code: ' +
                                                error.status +
                                                ", " +
                                                error.statusText +
                                                "...</a>",
                                        });
                                    },
                                });
                            }
                        })
                    };
                }
            });
        });
    </script>

@endsection
@endcomponent

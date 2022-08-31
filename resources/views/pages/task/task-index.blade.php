@component('layouts.app')

    @section('page-library-scripts')
        <script src="{{ asset('vendor/datetimepicker/build/jquery.datetimepicker.full.min.js') }}"></script>
    @endsection

    @slot('page_body')
        <div class="row">
            <div class="col-12">
                <div class="card p-2">
                    <div class=" border-bottom mb-1">
                        <div class="d-flex justify-content-between row">
                            <div class="col-lg-4 col-12 mb-0">
                                <div class="form-group">
                                    <label for="filter_by">Choose Filter By</label>
                                    <select name="filter_by" id="filter_by" class="form-control">
                                        @if (is_array($filters) && count($filters))
                                            <option value="" selected>Select Filter By</option>
                                            @foreach ($filters as $filter)
                                                <option value="{{ $filter }}">
                                                    {{ $filter }}</option>
                                            @endforeach
                                        @else
                                            <option value="" disabled selected>No data available!.</option>
                                        @endif

                                    </select>
                                </div>

                            </div>
                            <div class="col-lg-4 col-12 mb-0">
                                <div class="form-group">
                                    <label for="period">Choose Period</label>
                                    <select name="period" id="period" class="form-control">
                                        @if (is_array($periods) && count($periods))
                                            <option value="" selected>Select Period</option>
                                            @foreach ($periods as $period)
                                                <option value="{{ $period }}">
                                                    {{ $period }}</option>
                                            @endforeach
                                        @else
                                            <option value="" disabled selected>No data available!.</option>
                                        @endif

                                    </select>
                                </div>

                            </div>
                            <div class="col-lg-4 col-12 mb-0">
                                <div class="form-group">
                                    <label for="filter_task_status">Filter by Task Status</label>
                                    <div class="filter_task_status"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="border-top table datatables" id="datatables" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Due Date') }}</th>
                                    <th>{{ __('Task Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div id="dynamic_modal"></div>
        <!-- /Modal -->
    @endslot

    @section('page-scripts')
        <script>
            let datatable_url = "{{ route('task.datatable') }}";
            let create_url = "{{ route('task.create') }}";
            let store_url = "{{ route('task.store') }}";
            let show_url = "{{ route('task.show', ':id') }}";
            let edit_url = "{{ route('task.edit', ':id') }}";
            let update_url = "{{ route('task.update', ':id') }}";
            let destroy_url = "{{ route('task.destroy', ':id') }}";
            let mark_completed_url = "{{ route('task.mark-as-completed', ':id') }}";

            let csrf_token = "{{ csrf_token() }}";
        </script>
        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                });

                let filter_by = null;
                $('#filter_by').change(function() {
                    filter_by = $(this).find("option:selected").attr('value');
                    $('.datatables').DataTable().draw(true);
                });

                let period = null;
                $('#period').change(function() {
                    period = $(this).find("option:selected").attr('value');
                    $('.datatables').DataTable().draw(true);
                });

                let table = $('#datatables').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        "data": function(d) {
                            d._token = csrf_token;
                            d.filter_by = filter_by;
                            d.period = period;
                        },
                        method: 'POST',
                        url: datatable_url,
                        error: function(error) {
                            Swal.fire({
                                icon: error.responseJSON.icon,
                                title: error.responseJSON.title,
                                text: error.responseJSON.message,
                                footer: '<a href="">Error Code: ' +
                                    error.status +
                                    ", " +
                                    error.statusText +
                                    "...</a>",
                            });
                        }
                    },
                    columns: [{
                            data: "title",
                            name: "title",
                        },
                        {
                            data: "description",
                            name: "description",
                        },
                        {
                            data: "due_date",
                            name: "due_date",
                        },
                        {
                            data: "task_status",
                            name: "task_status",
                        },
                        {
                            data: "actions",
                            name: "actions",
                        },
                    ],
                    order: [],
                    dom: '<"row mx-1"<"col-sm-12 col-md-3" l><"col-sm-12 col-md-9"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-wrap me-1"<"me-3"f>B>>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    language: {
                        sLengthMenu: "_MENU_",
                        search: "Search",
                        searchPlaceholder: "Search..",
                    },
                    buttons: [{
                        text: "Create Task",
                        className: " btn ml-1",
                        init: function(e, a, t) {
                            $(a).removeClass("btn-secondary");
                        },
                        action: function() {
                            $.ajax({
                                url: create_url,
                                type: "GET",
                                success: function(data) {
                                    if (!$("#createTaskModal").is(":visible")) {
                                        $("#dynamic_modal").html(data.view);
                                        $("#createTaskModal").modal("show");
                                    }

                                    $('#due_date').datetimepicker({
                                        format: 'Y-m-d H:i',
                                    });

                                    actionCreate();
                                },
                                error: function(error) {
                                    Swal.fire({
                                        icon: error.responseJSON.icon,
                                        title: error.responseJSON.title,
                                        text: error.responseJSON.message,
                                        footer: '<a href="">Error Code: ' +
                                            error.status +
                                            ", " +
                                            error.statusText +
                                            "...</a>",
                                    });
                                },
                            });
                        },
                    }, ],
                    initComplete: function() {
                        this.api()
                            .columns(3)
                            .every(function() {
                                var t = this,
                                    a = $(
                                        '<select id="filter_task_status" class="form-control"><option value=""> Select Category </option></select>'
                                    )
                                    .appendTo(".filter_task_status")
                                    .on("change", function() {
                                        var e =
                                            $.fn.dataTable.util.escapeRegex(
                                                $(this).val()
                                            );
                                        t.search(
                                            e ? "^" + e + "$" : "",
                                            !0,
                                            !1
                                        ).draw();
                                    });
                                let task_statuses = @json($task_statuses);
                                task_statuses.forEach(element => {
                                    a.append(
                                        '<option value="' +
                                        element +
                                        '">' +
                                        element +
                                        "</option>"
                                    );
                                });
                            });
                    },
                });
                $(".datatables tbody").on(
                        "click",
                        ".set-is-completed",
                        function() {
                            var id = $(this).attr("data-id");

                            Swal.fire({
                                title: "Mark this task completed?",
                                text: "You won't be able to revert this!",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Confirm!",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    let url_mark_completed = mark_completed_url;
                                    url_mark_completed = url_mark_completed.replace(":id", id);
                                    $.ajax({
                                        data: {
                                            '_token': csrf_token,
                                        },
                                        url: url_mark_completed,
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
                                            table.draw();
                                        },
                                        error: function(error) {
                                            console.log(error);
                                            Swal.fire({
                                                icon: error.responseJSON.icon,
                                                title: error.responseJSON.title,
                                                text: error.responseJSON.message,
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

                        }
                    ),
                    $(".datatables tbody").on(
                        "click",
                        ".edit-record",
                        function() {
                            var id = $(this).attr("data-id");
                            let url_edit = edit_url;
                            url_edit = url_edit.replace(":id", id);
                            let url_update = update_url;
                            url_update = url_update.replace(":id", id);
                            $.ajax({
                                url: url_edit,
                                type: "GET",
                                success: function(data) {
                                    if (!$("#updateTaskModal").is(":visible")) {
                                        $("#dynamic_modal").html(data.view);
                                        $("#updateTaskModal").modal("show");
                                    }
                                    $('#due_date').datetimepicker({
                                        format: 'Y-m-d H:i',
                                    });

                                    actionUpdate(url_update);
                                },
                                error: function(error) {
                                    Swal.fire({
                                        icon: error.responseJSON.icon,
                                        title: error.responseJSON.title,
                                        text: error.responseJSON.message,
                                        footer: '<a href="">Error Code: ' +
                                            error.status +
                                            ", " +
                                            error.statusText +
                                            "...</a>",
                                    });
                                },
                            });
                        }
                    ), $(".datatables tbody").on(
                        "click",
                        ".detail-record",
                        function() {
                            var id = $(this).attr("data-id");
                            let url_show = show_url;
                            url_show = url_show.replace(":id", id);

                            $.ajax({
                                url: url_show,
                                type: "GET",
                                success: function(data) {
                                    if (!$("#showTaskModal").is(":visible")) {
                                        $("#dynamic_modal").html(data.view);
                                        $("#showTaskModal").modal("show");
                                    }

                                },
                                error: function(error) {
                                    Swal.fire({
                                        icon: error.responseJSON.icon,
                                        title: error.responseJSON.title,
                                        text: error.responseJSON.message,
                                        footer: '<a href="">Error Code: ' +
                                            error.status +
                                            ", " +
                                            error.statusText +
                                            "...</a>",
                                    });
                                },
                            });
                        }
                    ), $(".datatables tbody").on(
                        "click",
                        ".delete-record",
                        function() {
                            Swal.fire({
                                title: "Are you sure?",
                                text: "You won't be able to revert this!",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Yes, delete it!",
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    var group_name = $(this).attr("data-id");
                                    let url_delete = destroy_url;
                                    url_delete = url_delete.replace(":id", group_name);
                                    $.ajax({
                                        data: {
                                            '_token': csrf_token,
                                        },
                                        dataType: "json",
                                        url: url_delete,
                                        type: "DELETE",
                                        success: function(data) {
                                            Swal.fire({
                                                timer: 3000,
                                                timerProgressBar: true,
                                                icon: data.icon,
                                                title: data.title,
                                                text: data.message,
                                                footer: "Alert will close automatically!.",
                                            });
                                            table.draw();
                                        },
                                        error: function(error) {
                                            console.log(error);
                                            Swal.fire({
                                                icon: error.responseJSON.icon,
                                                title: error.responseJSON.title,
                                                text: error.responseJSON.message,
                                                footer: '<a href="">Error Code: ' +
                                                    error.status +
                                                    ", " +
                                                    error.statusText +
                                                    "...</a>",
                                            });
                                        },
                                    });
                                }
                            });
                        }
                    );



                function actionCreate() {
                    $('#btnCreate').click(function(e) {
                        e.preventDefault();
                        $.ajax({
                            data: $("#form-input-task").serialize(),
                            url: store_url,
                            type: "POST",
                            dataType: "json",
                            success: function(data) {
                                if ($("#createTaskModal").is(":visible")) {
                                    $("#createTaskModal").modal("hide");
                                }
                                Swal.fire({
                                    timer: 3000,
                                    timerProgressBar: true,
                                    icon: data.icon,
                                    title: data.title,
                                    text: data.message,
                                    footer: "Alert will close automatically!.",
                                });
                                table.draw();
                            },
                            error: function(error) {
                                console.log(error);
                                Swal.fire({
                                    icon: error.responseJSON.icon,
                                    title: error.responseJSON.title,
                                    text: error.responseJSON.message,
                                    footer: '<a href="">Error Code: ' +
                                        error.status +
                                        ", " +
                                        error.statusText +
                                        "...</a>",
                                });
                            },
                        });
                    });
                }

                function actionUpdate(url) {
                    $('#btnUpdate').click(function(e) {
                        e.preventDefault();
                        $.ajax({
                            data: $("#form-input-task").serialize(),
                            url: url,
                            type: "PUT",
                            dataType: "json",
                            success: function(data) {
                                if ($("#updateTaskModal").is(":visible")) {
                                    $("#updateTaskModal").modal("hide");
                                }
                                Swal.fire({
                                    timer: 3000,
                                    timerProgressBar: true,
                                    icon: data.icon,
                                    title: data.title,
                                    text: data.message,
                                    footer: "Alert will close automatically!.",
                                });
                                table.draw();
                            },
                            error: function(error) {
                                console.log(error);
                                Swal.fire({
                                    icon: error.responseJSON.icon,
                                    title: error.responseJSON.title,
                                    text: error.responseJSON.message,
                                    footer: '<a href="">Error Code: ' +
                                        error.status +
                                        ", " +
                                        error.statusText +
                                        "...</a>",
                                });
                            },
                        });
                    });
                }
            });
        </script>
    @endsection
@endcomponent

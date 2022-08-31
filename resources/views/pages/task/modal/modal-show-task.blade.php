<div class="modal fade" id="showTaskModal" aria-hidden="true" tabindex="-1" role="dialog" data-backdrop="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-md-4 p-3">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body">
                <div class="mb-2 text-center">
                    <h3>Detail Task</h3>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <ul class="list-unstyled list-unstyled-border list-unstyled-noborder">
                                    <li class="media">
                                        <div class="media-body">
                                            <div class="media-right">
                                                <div class="text-primary">
                                                    {{ isset($task_status) ? $task_status : 'Unknown' }}</div>
                                            </div>
                                            <div class="media-title mb-1">Created By
                                                {{ isset($creator) ? $creator : 'John Doe' }}</div>
                                            <div class="text-time"> Created,
                                                {{ isset($created_at) ? \Carbon\Carbon::parse($created_at)->format('d M Y, H:i') : '-' }}
                                            </div>
                                            <div class="media-description text-muted">
                                                <ul class="list-inline">
                                                    <li class="list-inline-item"><u>{{ $task_title }}</u> => </li>
                                                    <li class="list-inline-item">{{ $task_description }}</li>
                                                </ul>

                                            </div>
                                            <div class="media-links">
                                                <div class="bullet"></div>
                                                <a>Finished Task At:
                                                    {{ isset($finished_at) ? \Carbon\Carbon::parse($finished_at)->format('d M Y, H:i') : '-' }}
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 demo-vertical-spacing text-center">
                        <button type="reset" class="btn btn-secondary mb-1" data-dismiss="modal"
                            aria-label="Close">Discard</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

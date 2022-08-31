<div class="modal fade" id="updateAdditionalTaskModal" aria-hidden="true" tabindex="-1" role="dialog" data-backdrop="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-md-4 p-3">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body">
                <div class="mb-2 text-center">
                    <h3>Create Additional Task</h3>
                </div>
                <form id="form-input-additional-task" class="row">
                    @csrf
                    @include('pages.task.additional-task.modal._form-input-additional-task')
                    @include('pages.task.modal._form-input-task')
                    <div class="col-12 demo-vertical-spacing text-center">
                        <button type="reset" class="btn btn-secondary mb-1" data-dismiss="modal"
                            aria-label="Close">Discard</button>
                        <button type="button" id="btnUpdate" class="btn btn-primary me-sm-3 me-1 mb-1">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

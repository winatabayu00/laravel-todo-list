<div class="col-12 mb-1">
    <label class="form-label" for="name">Task Title</label>
    <input type="text" id="title" name="title" class="form-control" placeholder="Task Name" autofocus
        autocomplete="off" value="{{ !empty($task) && $task !== null ? $task->title : null }}" />
</div>
<div class="col-12 mb-1">
    <label class="form-label" for="description">Task Description</label>
    <input type="text" id="description" name="description" class="form-control" placeholder="Task Description"
        autofocus autocomplete="off" value="{{ !empty($task) && $task !== null ? $task->description : null }}" />
</div>
<div class="col-12 mb-1">
    <label class="form-label" for="due_date">Due</label>
    <input type="input" id="due_date" name="due_date" class="form-control" placeholder="Task Due" autofocus
        autocomplete="off"
        value="{{ !empty($task) && $task !== null ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d H:i') : null }}" />
</div>
<div class="col-12 mb-1">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="set_as_priority" id="set_as_priority"
            {{ !empty($task) && $task !== null && $task->is_priority ? 'checked' : '' }}>
        <label class="form-check-label" for="set_as_priority">
            Set as Priority
        </label>
    </div>
</div>

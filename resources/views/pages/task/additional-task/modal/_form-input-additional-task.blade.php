<div class="col-12 mb-1">
    <label class="form-label" for="receiver_id">Assign To</label>
    <select name="receiver_id" id="receiver_id" class="form-control" style="width: 100%">
        @if (!empty($assign_to_users) && $assign_to_users->count() > 0)
            @foreach ($assign_to_users as $user)
                <option value="{{ $user->id }}"
                    {{ !empty($additional_task) && $additional_task !== null && $additional_task->receiver_id == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}</option>
            @endforeach
        @else
            <option value="" selected disabled>No reference found!</option>
        @endif
    </select>
</div>

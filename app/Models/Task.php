<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'title',
        'description',
        'due_date',
        'is_priority',
    ];

    /* to know who owner the todo */
    public function task_owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /* to know is task from addional or self create */
    public function additional_task(): HasMany
    {
        return $this->hasMany(AdditionalTask::class);
    }

    /* to know who sender the addtional task */
    public function additional_task_sender(): HasManyThrough
    {
        return $this->hasManyThrough(task::class, AdditionalTask::class, 'sender_id');
    } // task -> additional task -> user
}

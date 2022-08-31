<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdditionalTask extends Model
{
    use HasFactory;

    const status_pending = 'Pending'; // waiting action from the user
    const status_accepted = 'Accepted'; // only user who have role manager and employee (role level 2 & 1)
    const status_decline = 'Decline'; // only user who have role manager and employee (role level 2 & 1)
    const status_pospone = 'Pospone'; // only user who have role manager and employee (role level 2 & 1)
    const status_canceled = 'Canceled'; // only user who have role manager and bos (role level 1 & 2)

    protected $fillable = [
        'sender_id', //it will deleted
        'task_id', //it will deleted
        'receiver_id',
        'additional_task_status',
    ];

    protected $guard = [
        'notes',
        'pospone_until',
    ];

    /* to get task data */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /* to get who send the addtional task */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /* to get who will recieve the addtional task */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}

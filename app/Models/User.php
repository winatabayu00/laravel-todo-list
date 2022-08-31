<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /* to get all my tasks */
    public function my_tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /* to get all my additional tasks */
    public function my_additional_tasks(): HasMany
    {
        return $this->hasMany(AdditionalTask::class, 'receiver_id');
    }

    /* to get all sendding additional tasks created by me */
    public function sending_additional_tasks(): HasManyThrough
    {
        return $this->hasManyThrough(AdditionalTask::class, Task::class, 'user_id', 'task_id');
    } // User -> additinal task -> task
}

<?php

namespace App\Providers;

use App\Actions\Task\DeleteTask;
use App\Actions\Task\NewTask;
use App\Actions\Task\UpdateTask;
use App\Contracts\Task\NewTaskInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton(NewTask::class);
        app()->singleton(UpdateTask::class);
        app()->singleton(DeleteTask::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

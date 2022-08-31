<?php

use App\Models\AdditionalTask;
use App\Models\Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id')->comment('automatic get from auth user'); // cannot be null
            $table->unsignedBigInteger('receiver_id'); // cannot be null
            $table->foreignIdFor(Task::class)->constrained()
                ->onUpdate('cascade')->onDelete('cascade');
            $table->string('additional_task_status', 8)->default(AdditionalTask::status_pending);
            $table->text('notes')->nullable();
            $table->datetime('pospone_until')->nullable()->comment('will filled when task status is pospone');
            $table->timestamps();
            $table->softDeletes();

            /* Add foreigin key */
            $table->foreign('sender_id')->on('users')->references('id')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('receiver_id')->on('users')->references('id')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('additional_tasks');
    }
}

<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained()
                ->onUpdate('cascade')->onDelete('cascade');

            // $table->string('group', 20);
            $table->string('title', 30)->unique(); // unique??
            $table->text('description');
            $table->dateTime('due_date');
            $table->boolean('is_priority')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->dateTime('submited_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}

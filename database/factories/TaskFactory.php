<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'group' => Str::random(10),
            'title' => Str::random(10),
            'description' => Str::random(10),
            'due_date' => Carbon::now()->addHour(2),
        ];
    }
}

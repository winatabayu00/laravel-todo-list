<?php

namespace Database\Seeders;

use App\Models\Spatie\Role;
use Illuminate\Database\Seeder;

class SeedPermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* seed default role */
        foreach (Role::starter_role_list() as $key => $value) {
            Role::query()->create(['name' => $value]);
        }
    }
}

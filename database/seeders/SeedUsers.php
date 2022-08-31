<?php

namespace Database\Seeders;

use App\Models\Spatie\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SeedUsers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array_users = [
            [
                'name' => ucwords('winata bayu'),
                'email' => 'winatabayu01@gmail.com',
                'password' => Hash::make('bayu'),
            ], [
                'name' => ucwords('winata bayu'),
                'email' => 'winatabayu02@gmail.com',
                'password' => Hash::make('bayu'),
            ],
        ];

        foreach ($array_users as $key => $value) {
            $user = User::query()->create($value);
            if ($user->id == 1) {
                $user->assignRole(Role::role_manager);
            } else {
                $user->assignRole(Role::role_employee);
            }
        }
    }
}

<?php

/* dataset('usersdataset', function () {
    return ['usersdataset A', 'usersdataset B'];
}); */

use App\Models\Spatie\Role;
use App\Models\User;

dataset('user', function () {
    yield fn () => User::factory()->create();
});
dataset('user_boss', function () {
    yield fn () => User::factory()->create()->assignRole(Role::role_boss);
});
dataset('user_manager', function () {
    yield fn () => User::factory()->create()->assignRole(Role::role_manager);
});
dataset('user_employee', function () {
    yield fn () => User::factory()->create()->assignRole(Role::role_employee);
});

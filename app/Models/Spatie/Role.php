<?php

namespace App\Models\Spatie;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;

    const role_boss = 'Boss';
    const role_manager = 'Manager';
    const role_employee = 'Employee';

    public static function starter_role_list(): array
    {
        return [
            self::role_boss,
            self::role_manager,
            self::role_employee,
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => Constants::ADMIN_ROLE,
                'guard_name' => 'api',
            ],
            [
                'name' => Constants::USER_ROLE,
                'guard_name' => 'api',
            ]
        ];
        Role::insert($roles);
    }
}

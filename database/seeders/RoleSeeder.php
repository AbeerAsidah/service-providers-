<?php

namespace Database\Seeders;

use App\Constants\Constants;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
            ],
            [
                'name' => Constants::SERVICE_PROVIDER_ROLE,
                'guard_name' => 'api',
            ]
        ];
        Role::insert($roles);

        $admin = User::create([
            'username' => 'admin',
            'email' => 'admin123@gmail.com',
            'name' => 'admin',
            'phone_number' => '+999999999',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('12345678'),
            'is_active' => '1',
        ]);
        $admin->assignRole(Constants::ADMIN_ROLE);

    }
}

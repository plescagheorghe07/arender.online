<?php

namespace Database\Seeders;

use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');

        if (! is_string($password) || $password === '') {
            return;
        }

        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@arender.online')],
            [
                'name' => 'Admin',
                'password' => $password,
                'is_admin' => true,
                'role' => StaffRole::Admin,
            ],
        );
    }
}

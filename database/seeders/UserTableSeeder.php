<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username'          => 'Nurhaliza Admin',
                'email'             => 'nurhalizaadmin@gmail.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('nurhaliza123'),
                'role'              => 'admin',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}

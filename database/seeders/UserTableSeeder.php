<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
                'username'          => 'bintang_admin',
                'email'             => 'muhbintang650@gmail.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('bintang123'),
                'role'              => 'developer',
            ],
            [
                'username'          => 'fery_admin',
                'email'             => 'feryfadulrahman@gmail.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('fery123'),
                'role'              => 'developer',
            ],
            [
                'username'          => 'nurhaliza_admin',
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

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'Bintang Admin',
                'email' => 'muhbintang650@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('bintang123'),
                'role' => 'admin',
            ],
            [
                'username' => 'Fery Admin',
                'email' => 'feryfadulrahman@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('fery123'),
                'role' => 'admin',
            ],
            [
                'username' => 'Nurhalizah Admin',
                'email' => 'nurhalizahadmin@gmail.com',
                'email_verified_at' => now(),
                'password' => bcrypt('nurhalizah123'),
                'role' => 'admin',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}

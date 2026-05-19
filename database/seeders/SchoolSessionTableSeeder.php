<?php

namespace Database\Seeders;

use App\Models\SchoolSession;
use Illuminate\Database\Seeder;

class SchoolSessionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sessions = [
            [
                'session_name' => 'Sesi 1',
                'start_time'   => '07:30:00',
                'end_time'     => '09:00:00',
                'description'  => 'Sesi pelajaran pertama pagi hari.',
                'status_active'=> true,
            ],
            [
                'session_name' => 'Sesi 2',
                'start_time'   => '09:15:00',
                'end_time'     => '10:45:00',
                'description'  => 'Sesi pelajaran kedua setelah istirahat pertama.',
                'status_active'=> true,
            ],
            [
                'session_name' => 'Sesi 3',
                'start_time'   => '11:00:00',
                'end_time'     => '12:30:00',
                'description'  => 'Sesi pelajaran ketiga sebelum istirahat siang.',
                'status_active'=> true,
            ],
            [
                'session_name' => 'Sesi 4',
                'start_time'   => '13:00:00',
                'end_time'     => '14:30:00',
                'description'  => 'Sesi pelajaran keempat setelah istirahat siang.',
                'status_active'=> true,
            ],
            [
                'session_name' => 'Sesi 5',
                'start_time'   => '14:45:00',
                'end_time'     => '16:15:00',
                'description'  => 'Sesi pelajaran kelima sore hari.',
                'status_active'=> true,
            ],
        ];

        foreach ($sessions as $session) {
            SchoolSession::create($session);
        }
    }
}

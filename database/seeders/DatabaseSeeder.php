<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $isDeleteFile = config('const.auto_delete_file');

        if ($isDeleteFile) {
            // delete old files and directories
            File::deleteDirectory(public_path('storage/avatars'));
            File::deleteDirectory(public_path('storage/student-photos'));

            // delete old directories in storage
            Storage::deleteDirectory('public/avatars');
            Storage::deleteDirectory('public/student-photos');
        }

        // call the seeders
        if (env('APP_ENV') == 'production') {
            $this->call([
                UserTableSeeder::class,
            ]);
        } else {
            $this->call([
                UserTableSeeder::class,
                ClassRoomTableSeeder::class,
                SubjectStudyTableSeeder::class,
                TeacherTableSeeder::class,
                StudentTableSeeder::class,
                StudentInSchoolSeeder::class,
                ClassScheduleTableSeeder::class,
                ClassAttendanceTableSeeder::class,
                CheckInRecordTableSeeder::class,
                CheckOutRecordTableSeeder::class,
                StudentAttendanceTableSeeder::class,
                StudentGuardianTableSeeder::class,
            ]);
        }
    }
}

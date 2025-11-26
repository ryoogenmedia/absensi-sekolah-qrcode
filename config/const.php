<?php

return [
    'roles' => [
        'admin',
        'siswa',
        'guru',
        'wali siswa',
    ],

    'guardian_relationships' => [
        'ayah',
        'ibu',
        'wali',
        'saudara',
        'lainnya',
    ],

    'attendance_status' => [
        'hadir',
        'alpa',
        'izin',
        'sakit',
    ],

    'name_days' => [
        'senin',
        'selasa',
        'rabu',
        'kamis',
        'jumat',
        'sabtu',
        'ahad',
    ],

    'name_days_secound' => [
        'senin',
        'selasa',
        'rabu',
        'kamis',
        'jumat',
        'sabtu',
    ],

    'sex' => [
        'laki-laki',
        'perempuan',
    ],

    'secret_email' => [
        'muhbintang650@gmail.com',
        'feryfadulrahman@gmail.com',
    ],

    'religions' => [
        'islam',
        'kristen',
        'katolik',
        'protestan',
        'hindu',
        'budha',
        'konghucu',
        'kepercayaan yang maha esa',
    ],

    'class_room_examples' => [
        'I',
        'II',
        'III',
        'IV',
        'V',
        'VI',
        'VII',
        'VIII',
        'IX',
        'X',
        'XI',
        'XII',
        'XIII',
    ],

    'subject_study_examples' => [
        'Matematika',
        'Bahasa Indonesia',
        'Bahasa Inggris',
        'Ilmu Pengetahuan Alam',
        'Ilmu Pengetahuan Sosial',
        'Pendidikan Kewarganegaraan',
        'Pendidikan Agama',
        'Seni Budaya',
        'Pendidikan Jasmani',
        'Fisika',
        'Kimia',
        'Biologi',
        'Ekonomi',
        'Geografi',
        'Sejarah',
        'Sosiologi',
        'Teknologi Informasi',
        'Prakarya',
        'Bimbingan Konseling',
        'Kewirausahaan'
    ],

    'auto_delete_file' => env('AUTO_DELETE_FILE', false),

    'periods' => [
        'daily',
        'weekly',
        'monthly',
        'yearly',
    ],
];

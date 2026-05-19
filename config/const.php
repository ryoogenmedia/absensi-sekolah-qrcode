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

    'class_sessions' => [
        'sesi_1' => [
            'label' => 'Sesi 1 (07:30 - 09:00)',
            'start' => '07:30',
            'end' => '09:00',
        ],
        'sesi_2' => [
            'label' => 'Sesi 2 (09:15 - 10:45)',
            'start' => '09:15',
            'end' => '10:45',
        ],
        'sesi_3' => [
            'label' => 'Sesi 3 (11:00 - 12:30)',
            'start' => '11:00',
            'end' => '12:30',
        ],
        'sesi_4' => [
            'label' => 'Sesi 4 (13:00 - 14:30)',
            'start' => '13:00',
            'end' => '14:30',
        ],
        'sesi_5' => [
            'label' => 'Sesi 5 (14:45 - 16:15)',
            'start' => '14:45',
            'end' => '16:15',
        ],
    ],

    'sex' => [
        'laki-laki',
        'perempuan',
    ],

    'secret_email' => [],

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

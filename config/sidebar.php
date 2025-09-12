<?php

return [
    [
        'title' => 'Beranda',
        'icon' => 'home',
        'route-name' => 'home',
        'is-active' => 'home',
        'description' => 'Untuk melihat ringkasan aplikasi.',
        'roles' => ['admin', 'operator','guru','siswa'],
    ],

    // SISWA

    [
        'title' => 'Kehadiran Anda',
        'icon' => 'calendar-check',
        'route-name' => 'presence-student.index',
        'is-active' => 'presence-student.index',
        'description' => 'Untuk melihat kehadiran anda.',
        'roles' => ['siswa'],
    ],

    [
        'title' => 'Jadwal Mapel',
        'icon' => 'calendar-alt',
        'route-name' => 'schedule-student.index',
        'is-active' => 'schedule-student.index',
        'description' => 'Untuk melihat jadwal mapel anda.',
        'roles' => ['siswa'],
    ],

    // GURU

    [
        'title' => 'Jadwal Mengajar',
        'icon' => 'clock',
        'route-name' => 'schedule-teacher.index',
        'is-active' => 'schedule-teacher*',
        'description' => 'Untuk melihat jadwal mengajar anda.',
        'roles' => ['guru'],
    ],

    [
        'title' => 'Presensi Kelas',
        'icon' => 'address-card',
        'route-name' => 'class-attendance.index',
        'is-active' => 'class-attendance*',
        'description' => 'Untuk melakukan presensi kelas.',
        'roles' => ['guru'],
    ],

    // ADMIN

    [
        'title' => 'Master',
        'description' => 'Menampilkan data master.',
        'icon' => 'database',
        'route-name' => 'master.admin.index',
        'is-active' => 'master*',
        'roles' => ['admin'],
        'sub-menus' => [
            [
                'title' => 'Admin',
                'description' => 'Melihat daftar admin.',
                'route-name' => 'master.admin.index',
                'is-active' => 'master.admin*',
            ],
            [
                'title' => 'Ruang Kelas',
                'description' => 'Melihat daftar ruang kelas.',
                'route-name' => 'master.classroom.index',
                'is-active' => 'master.classroom*',
            ],
            [
                'title' => 'Jadwal Kelas',
                'description' => 'Melihat jadwal kelas.',
                'route-name' => 'master.class-schedule.index',
                'is-active' => 'master.class-schedule*',
            ],
            [
                'title' => 'Wali Kelas',
                'description' => 'Melihat daftar wali kelas tiap kelas.',
                'route-name' => 'master.advisor-class.index',
                'is-active' => 'master.advisor-class*',
            ],
            [
                'title' => 'Mata Pelajaran',
                'description' => 'Melihat daftar ruang kelas.',
                'route-name' => 'master.subject-study.index',
                'is-active' => 'master.subject-study*',
            ],
        ],
    ],

    [
        'title' => 'Guru',
        'icon' => 'user-tie',
        'route-name' => 'teacher.index',
        'is-active' => 'teacher*',
        'description' => 'Melihat daftar guru.',
        'roles' => ['admin'],
    ],

    [
        'title' => 'Guru Mata Pelajaran',
        'icon' => 'chalkboard-teacher',
        'route-name' => 'subject-teacher.index',
        'is-active' => 'subject-teacher*',
        'description' => 'Melihat mata pelajaran guru.',
        'roles' => ['admin'],
    ],

    [
        'title' => 'Siswa',
        'icon' => 'graduation-cap',
        'route-name' => 'student.index',
        'is-active' => 'student*',
        'description' => 'Melihat daftar siswa.',
        'roles' => ['admin'],
    ],

    [
        'title' => 'Qr Code',
        'icon' => 'qrcode',
        'route-name' => 'qrcode.index',
        'is-active' => 'qrcode*',
        'description' => 'Melihat daftar qr code.',
        'roles' => ['admin'],
    ],

    [
        'title' => 'Scan Qr',
        'icon' => 'clock',
        'route-name' => 'scan-qr.index',
        'is-active' => 'scan-qr*',
        'description' => 'Untuk Scan Qr Code',
        'roles' => ['admin','operator'],
    ],

    [
        'title' => 'Whatsapp Broadcast',
        'icon' => 'whatsapp',
        'brand_icon' => true,
        'route-name' => 'whatsapp-broadcast.index',
        'is-active' => 'whatsapp-broadcast*',
        'description' => 'Pengaturan whatsapp broadcast.',
        'roles' => ['admin'],
    ],

    [
        'title' => 'Presensi',
        'description' => 'Menampilkan daftar presensi pada aplikasi.',
        'icon' => 'calendar-check',
        'route-name' => 'attendance.class.index',
        'is-active' => 'attendance*',
        'roles' => ['admin'],
        'sub-menus' => [
            [
                'title' => 'Presensi Kelas',
                'description' => 'Melihat daftar presensi kelas.',
                'route-name' => 'attendance.class.index',
                'is-active' => 'attendance.class*',
            ],
            [
                'title' => 'Presensi Qrcode',
                'description' => 'Melihat daftar presensi Qr Code.',
                'route-name' => 'attendance.qrcode.check-in',
                'is-active' => 'attendance.qrcode*',
                'sub-menus' => [
                    [
                        'title' => 'Masuk',
                        'description' => 'Melihat daftar presensi masuk.',
                        'route-name' => 'attendance.qrcode.check-in',
                        'is-active' => 'attendance.qrcode.check-in',
                    ],
                    [
                        'title' => 'Keluar',
                        'description' => 'Melihat daftar presensi keluar.',
                        'route-name' => 'attendance.qrcode.check-out',
                        'is-active' => 'attendance.qrcode.check-out',
                    ],
                ],
            ],
        ],
    ],

    [
        'title' => 'Pengaturan',
        'description' => 'Menampilkan pengaturan aplikasi.',
        'icon' => 'cog',
        'route-name' => 'setting.profile.index',
        'is-active' => 'setting*',
        'roles' => ['admin', 'guru','operator','siswa'],
        'sub-menus' => [
            [
                'title' => 'Profil',
                'description' => 'Melihat pengaturan profil.',
                'route-name' => 'setting.profile.index',
                'is-active' => 'setting.profile*',
            ],
            [
                'title' => 'Akun',
                'description' => 'Melihat pengaturan akun.',
                'route-name' => 'setting.account.index',
                'is-active' => 'setting.account*',
            ],
        ],
    ],
];

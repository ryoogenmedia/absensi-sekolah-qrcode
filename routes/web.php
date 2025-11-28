<?php

use App\Http\Controllers\CetakPdfController;
use App\Http\Controllers\PrintReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/login');

/**
 * cetak pdf
 */
Route::middleware('auth', 'verified', 'force.logout')->prefix('cetak-pdf')->name('print-pdf.')->group(function () {
    Route::get('/kartu', [CetakPdfController::class, 'card'])->middleware('roles:admin,developer')->name('card');
});


Route::middleware('auth', 'verified', 'force.logout')->namespace('App\Livewire')->group(function () {

    /**
     * Print Report / Cetak Laporan
     */
    Route::prefix('cetak-laporan')->middleware('roles:admin,developer')->name('print-report.')->group(function () {
        Route::get('/siswa', [PrintReportController::class, 'student'])
            ->name('student');

        Route::get('/guru', [PrintReportController::class, 'teacher'])
            ->name('teacher');

        Route::get('/jadwal-kelas', [PrintReportController::class, 'classSchedule'])
            ->name('class-schedule');

        Route::get('/mata-pelajaran', [PrintReportController::class, 'subjectStudy'])
            ->name('subject-study');

        Route::get('/presensi/kelas', [PrintReportController::class, 'attendanceClass'])
            ->name('attendance.class');

        Route::get('/presensi/qrcode/masuk', [PrintReportController::class, 'attendanceQrcodeCheckIn'])
            ->name('attendance.qrcode.check-in');

        Route::get('/presensi/qrcode/keluar', [PrintReportController::class, 'attendanceQrcodeCheckOut'])
            ->name('attendance.qrcode.check-out');
    });

    /**
     *  report / laporan
     */
    Route::prefix('laporan')->name('report.')->middleware('roles:admin,developer')->namespace('Report')->group(function () {
        /**
         * student report / laporan siswa
         */
        Route::prefix('siswa')->name('student.')->group(function () {
            Route::get('/', Student\Index::class)->name('index');
        });

        /**
         * teacher report / laporan guru
         */
        Route::prefix('guru')->name('teacher.')->group(function () {
            Route::get('/', Teacher\Index::class)->name('index');
        });

        /**
         * class schedule report / laporan jadwal kelas
         */
        Route::prefix('jadwal-kelas')->name('class-schedule.')->group(function () {
            Route::get('/', ClassSchedule\Index::class)->name('index');
        });

        /**
         * subject report / laporan mata pelajaran
         */
        Route::prefix('mata-pelajaran')->name('subject-study.')->group(function () {
            Route::get('/', SubjectStudy\Index::class)->name('index');
        });

        /**
         * attendance report / laporan presensi
         */
        Route::prefix('presensi')->name('attendance.')->group(function () {
            // class attendance report / laporan presensi kelas
            Route::prefix('kelas')->name('class.')->group(function () {
                Route::get('/', Attendance\Class\Index::class)->name('index');
            });

            //qrcode attendance report / laporan presensi qrcode
            Route::prefix('qrcode')->name('qrcode.')->group(function () {
                Route::get('/masuk', Attendance\Qrcode\CheckIn::class)->name('check-in');
                Route::get('/keluar', Attendance\Qrcode\CheckOut::class)->name('check-out');
            });
        });
    });

    /**
     * guardian student schedule / jadwal siswa wali
     */
    Route::prefix('jadwal-siswa-wali')->name('guardian-student-schedule.')->middleware('roles:wali siswa')->group(function () {
        Route::get('/', GuardianStudentSchedule\Index::class)->name('index');
    });

    /**
     * guardian student presence / presensi siswa wali
     */
    Route::prefix('presensi-siswa-wali')->name('guardian-student-presence.')->middleware('roles:wali siswa')->group(function () {
        Route::get('/', GuardianStudentPresence\Index::class)->name('index');
    });

    /**
     *  persence student / presensi siswa
     */
    Route::prefix('presensi-siswa')->name('presence-student.')->middleware('roles:siswa')->group(function () {
        Route::get('/', PersenceClassRoom\Index::class)->name('index');
    });

    /**
     * student schedule / jadwal siswa
     */
    Route::prefix('jadwal-siswa')->name('schedule-student.')->middleware('roles:siswa')->group(function () {
        Route::get('/', StudentSchedule\Index::class)->name('index');
    });

    /**
     * guardian stdudent / wali siswa
     */
    Route::prefix('wali-siswa')->name('guardian-student.')->middleware('roles:admin,developer')->group(function () {
        Route::get('/', StudentGuardian\Index::class)->name('index');
        Route::get('/tambah', StudentGuardian\Create::class)->name('create');
        Route::get('/sunting/{id}', StudentGuardian\Edit::class)->name('edit');
    });

    /**
     * master / data master
     */
    Route::prefix('master')->name('master.')->middleware('roles:admin,developer')->namespace('Master')->group(function () {
        Route::redirect('/', 'master/admin');

        /**
         * class schedule / jadwal kelas
         */
        Route::prefix('jadwal-kelas')->name('class-schedule.')->middleware('roles:admin,developer')->group(function () {
            Route::get('/', ClassSchedule\Index::class)->name('index');
            Route::get('/tambah', ClassSchedule\Create::class)->name('create');
            Route::get('/sunting/{id}', ClassSchedule\Edit::class)->name('edit');
        });

        /**
         * admin
         */
        Route::prefix('admin')->name('admin.')->middleware('roles:admin,developer')->group(function () {
            Route::get('/', Admin\Index::class)->name('index');
            Route::get('/tambah', Admin\Create::class)->name('create');
            Route::get('/{id}/edit', Admin\Edit::class)->name('edit');
        });

        /**
         * class room / ruang kelas
         */
        Route::prefix('ruang-kelas')->name('classroom.')->middleware('roles:admin,developer')->group(function () {
            Route::get('/', ClassRoom\Index::class)->name('index');
        });

        /**
         * subject study / mata pelajaran
         */
        Route::prefix('mata-pelajaran')->name('subject-study.')->middleware('roles:admin,developer')->group(function () {
            Route::get('/', SubjectStudy\Index::class)->name('index');
        });

        /**
         *  class advisor / wali kelas
         */
        Route::prefix('wali-kelas')->name('advisor-class.')->middleware('roles:admin,developer')->group(function () {
            Route::get('/', ClassAdvisor\Index::class)->name('index');
        });
    });

    // ATTENDANCE
    Route::prefix('presensi')->name('attendance.')->middleware('roles:admin,developer')->namespace('Attendance')->group(function () {
        /**
         * class / kelas
         */
        Route::prefix('kelas')->name('class.')->group(function () {
            Route::get('/', Class\Index::class)->name('index');
            Route::get('/detail/{id}', Class\Detail::class)->name('detail');
        });

        /**
         * qrcode / qr code
         */
        Route::prefix('qrcode')->name('qrcode.')->group(function () {
            Route::get('/masuk', Qrcode\CheckIn::class)->name('check-in');
            Route::get('/keluar', Qrcode\CheckOut::class)->name('check-out');
        });
    });

    /**
     * teacher / guru
     */
    Route::prefix('guru')->name('teacher.')->middleware('roles:admin,developer')->group(function () {
        Route::get('/', Teacher\Index::class)->name('index');
        Route::get('/tambah', Teacher\Create::class)->name('create');
        Route::get('/{id}/edit', Teacher\Edit::class)->name('edit');
        Route::get('/{id}/detail', Teacher\Detail::class)->name('detail');
    });

    /**
     * teacher subject / guru mata pelajaran
     */
    Route::prefix('mata-pelajaran-guru')->name('subject-teacher.')->middleware('roles:admin,developer')->group(function () {
        Route::get('/', TeacherSubject\Index::class)->name('index');
    });

    /**
     * student / student
     */
    Route::prefix('siswa')->name('student.')->middleware('roles:admin,developer')->group(function () {
        Route::get('/', Student\Index::class)->name('index');
        Route::get('/tambah', Student\Create::class)->name('create');
        Route::get('/{id}/edit', Student\Edit::class)->name('edit');
        Route::get('/{id}/detail', Student\Detail::class)->name('detail');
    });

    /**
     * qrcode / qrcode
     */
    Route::prefix('qr-code')->name('qrcode.')->middleware('roles:admin,developer')->group(function () {
        Route::get('/', Qrcode\Index::class)->name('index');
    });

    /** scan qr / camera */
    Route::prefix('scan-qr')->name('scan-qr.')->middleware('roles:operator,admin,developer')->group(function () {
        Route::get('/', ScanQr\Index::class)->name('index');
    });

    /** teacher schedule / jadwal mengajar dosen */
    Route::prefix('jadwal-mengajar')->name('schedule-teacher.')->middleware('roles:guru')->group(function () {
        Route::get('/', TeacherSchedule\Index::class)->name('index');
    });

    /**
     * beranda / home
     */
    Route::get('beranda', Home\Index::class)->name('home')
        ->middleware('roles:admin,siswa,guru,developer,wali siswa');

    /**
     * class attendance / presensi kelas
     */
    Route::prefix('presensi-kelas')->name('class-attendance.')->middleware('roles:guru')->group(function () {
        Route::get('/', ClassAttendance\Index::class)->name('index');
        Route::get('/detail/{id}', ClassAttendance\Detail::class)->name('detail');
        Route::get('/create/{id}', ClassAttendance\Create::class)->name('create');
        Route::get('/edit/{scheduleId}/{classAttendanceId}', ClassAttendance\Edit::class)->name('edit');
    });

    /**
     * whatsapp broadcast
     */
    Route::prefix('whatsapp-broadcast')->name('whatsapp-broadcast.')->middleware('roles:admin,developer')->group(function () {
        Route::get('/', WhatsappBroadcast\Index::class)->name('index');
    });

    /**
     * setting
     */
    Route::prefix('pengaturan')->name('setting.')->middleware('roles:admin,siswa,guru,developer,wali siswa')->namespace('Setting')->group(function () {
        Route::redirect('/', 'pengaturan/aplikasi');

        /**
         * Profile
         */
        Route::prefix('profil')->name('profile.')->group(function () {
            Route::get('/', Profile\Index::class)->name('index');
        });

        /**
         * Account
         */
        Route::prefix('akun')->name('account.')->group(function () {
            Route::get('/', Account\Index::class)->name('index');
        });
    });
});

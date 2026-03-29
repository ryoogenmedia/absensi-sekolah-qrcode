<?php

namespace App\Livewire\Report\Attendance\Class;

use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
#[Title('Laporan Presensi Kelas')]
class Index extends Component
{
    use WithBulkActions, WithPerPagePagination, WithCachedRows, WithSorting;

    public $tab = 'list';

    public $filters = [
        'search' => '',
        'kelas' => '',
        'startDate' => '',
        'endDate' => '',
    ];

    public function placeholder()
    {
        return <<<'HTML'
            <div class="container-fluid d-flex align-items-center justify-content-center bg-light" style="min-height: 100vh;">
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5">
                            <div class="text-center">
                                <div class="spinner-grow text-primary mb-4" role="status" style="width: 3.5rem; height: 3.5rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>

                                <h4 class="fw-bold text-dark">Menyiapkan Data</h4>
                                <p class="text-muted">Laporan Anda sedang diproses oleh sistem. <br> Halaman ini akan diperbarui secara otomatis.</p>

                                <div class="progress mt-4" style="height: 6px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-center text-muted mt-4 small">&copy; 2026 Dashboard System</p>
                </div>
            </div>
        HTML;
    }

    #[Computed()]
    public function class_rooms()
    {
        return ClassRoom::where('status_active', true)->get(['id', 'name_class']);
    }

    #[Computed()]
    public function rows()
    {
        $query = StudentAttendance::query()
            ->with([
                'student:id,full_name',
                'class_attendance.class_room:id,name_class',
                'class_attendance.class_schedule.teacher:id,name',
                'class_attendance.class_schedule.subject_study:id,name_subject'
            ])
            ->when($this->filters['startDate'], function ($query) {
                $query->whereHas('class_attendance', fn($q) => $q->where('created_at', '>=', Carbon::parse($this->filters['startDate'])->startOfDay()));
            })
            ->when($this->filters['endDate'], function ($query) {
                $query->whereHas('class_attendance', fn($q) => $q->where('created_at', '<=', Carbon::parse($this->filters['endDate'])->endOfDay()));
            })
            ->when($this->filters['kelas'], function ($query, $kelas) {
                $query->whereHas('class_attendance', fn($q) => $q->where('class_room_id', $kelas));
            })
            ->when($this->filters['search'], function ($query, $search) {
                $query->whereHas('student', fn($q) => $q->where('full_name', 'LIKE', "%{$search}%"));
            });

        return $this->applyPagination($query);
    }

    #[Computed()]
    public function summaryData()
    {
        $statuses = config('const.attendance_status');
        $startDate = $this->filters['startDate'] ? Carbon::parse($this->filters['startDate'])->startOfDay() : null;
        $endDate = $this->filters['endDate'] ? Carbon::parse($this->filters['endDate'])->endOfDay() : null;

        if ($this->filters['kelas']) {
            return Student::where('class_room_id', $this->filters['kelas'])
                ->withCount([
                    'student_attendances as total_hadir' => function ($query) use ($startDate, $endDate) {
                        $query->where('status_attendance', 'hadir')
                            ->when($startDate, fn($q) => $q->whereHas('class_attendance', fn($ca) => $ca->where('created_at', '>=', $startDate)))
                            ->when($endDate, fn($q) => $q->whereHas('class_attendance', fn($ca) => $ca->where('created_at', '<=', $endDate)));
                    }
                ])->get();
        } else {
            // Gunakan withCount melalui relasi hasManyThrough di Model ClassRoom untuk performa maksimal
            $query = ClassRoom::where('status_active', true)->select('id', 'name_class');

            foreach ($statuses as $status) {
                $val = is_array($status) ? $status['value'] : $status;
                $query->withCount(["student_attendances as count_{$val}" => function ($q) use ($val, $startDate, $endDate) {
                    $q->where('status_attendance', $val)
                        ->when($startDate, fn($sq) => $sq->whereHas('class_attendance', fn($ca) => $ca->where('created_at', '>=', $startDate)))
                        ->when($endDate, fn($sq) => $sq->whereHas('class_attendance', fn($ca) => $ca->where('created_at', '<=', $endDate)));
                }]);
            }
            return $query->get();
        }
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.report.attendance.class.index');
    }
}

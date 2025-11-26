<?php

namespace App\Livewire\Attendance\Qrcode;

use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\CheckOutRecord;
use App\Models\ClassRoom;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class CheckOut extends Component
{
    use WithBulkActions;
    use WithPerPagePagination;
    use WithCachedRows;
    use WithSorting;

    public $filters = [
        'search' => '',
        'startDate' => '',
        'endDate' => '',
        'waktuKeluar' => '',
        'nis' => '',
        'kelas' => '',
    ];

    public function deleteSelected()
    {
        $checkOut = CheckOutRecord::whereIn('id', $this->selected)->get();
        $deleteCount = $checkOut->count();

        foreach ($checkOut as $data) {
            $data->delete();
        }

        $this->reset();

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menghapus $deleteCount data presensi keluar.",
        ]);

        return redirect()->back();
    }

    #[Computed()]
    public function class_rooms()
    {
        return ClassRoom::where('status_active', true)->get(['id', 'name_class']);
    }

    #[On('muat-ulang')]
    #[Computed()]
    public function rows()
    {
        $query = CheckOutRecord::query()
            ->when(!$this->sorts, fn($query) => $query->first())
            ->when($this->filters['startDate'], function ($query, $startDate) {
                $query->where('attendance_date', '>=', $startDate);
            })
            ->when($this->filters['endDate'], function ($query, $endDate) {
                $query->where('attendance_date', '<=', $endDate);
            })
            ->when($this->filters['waktuKeluar'], function ($query, $waktuMasuk) {
                $query->where('check_out_time', '>=', $waktuMasuk);
            })
            ->when($this->filters['nis'], function ($query, $nis) {
                $query->whereHas('student', function ($q) use ($nis) {
                    $q->where('nis', $nis);
                });
            })
            ->when($this->filters['kelas'], function ($query, $kelas) {
                $query->whereHas('student', function ($q) use ($kelas) {
                    $q->where('class_room_id', $kelas);
                });
            })
            ->when($this->filters['search'], function ($query, $search) {
                $query->whereHas('student', function ($query) use ($search) {
                    $query->where('full_name', 'LIKE', "%$search%");
                });
            })->latest();

        return $this->applyPagination($query);
    }

    #[Computed()]
    public function allData()
    {
        return CheckOutRecord::all();
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function mount()
    {
        $now = Carbon::now()->format('Y-m-d');
        $this->filters['startDate'] = $now;
        $this->filters['endDate'] = $now;
    }

    public function render()
    {
        return view('livewire.attendance.qrcode.check-out');
    }
}

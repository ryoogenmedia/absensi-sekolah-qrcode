<?php

namespace App\Livewire\Master\SchoolSession;

use App\Livewire\Traits\DataTable\WithBulkActions;
use App\Livewire\Traits\DataTable\WithCachedRows;
use App\Livewire\Traits\DataTable\WithPerPagePagination;
use App\Livewire\Traits\DataTable\WithSorting;
use App\Models\SchoolSession;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use WithBulkActions;
    use WithPerPagePagination;
    use WithCachedRows;
    use WithSorting;

    public $filters = [
        'search' => '',
    ];

    // FORM DATA
    public $sessionName;
    public $startTime;
    public $endTime;
    public $description;
    public $statusActive = true;

    // MODAL INITIALIZATION
    public $modalCreate = false;
    public $modalEdit = false;

    // IDENTITY
    public $schoolSessionId;

    public function changeStatusActive($id)
    {
        $session = SchoolSession::findOrFail($id);
        $session->status_active = !$session->status_active;
        $session->save();

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil mengubah status aktif sesi sekolah.",
        ]);

        return redirect()->back();
    }

    public function closeModal()
    {
        $this->resetModal();
    }

    public function openModalCreate()
    {
        $this->resetModal();
        $this->modalCreate = true;
    }

    public function openModalEdit($id)
    {
        $this->resetModal();
        $session = SchoolSession::findOrFail($id);
        $this->schoolSessionId = $session->id;
        $this->sessionName = $session->session_name;
        $this->startTime = date('H:i', strtotime($session->start_time));
        $this->endTime = date('H:i', strtotime($session->end_time));
        $this->description = $session->description;
        $this->statusActive = (bool) $session->status_active;
        $this->modalEdit = true;
    }

    public function resetModal()
    {
        $this->reset([
            'modalCreate',
            'modalEdit',
            'schoolSessionId',
            'sessionName',
            'startTime',
            'endTime',
            'description',
            'statusActive',
        ]);
        $this->statusActive = true;
    }

    public function deleteSelected()
    {
        $sessions = SchoolSession::whereIn('id', $this->selected)->get();
        $deleteCount = $sessions->count();

        foreach ($sessions as $data) {
            $data->delete();
        }

        $this->reset();

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => "Berhasil menghapus $deleteCount data sesi sekolah.",
        ]);

        return redirect()->back();
    }

    #[On('muat-ulang')]
    #[Computed()]
    public function rows()
    {
        $query = SchoolSession::query()
            ->when(!$this->sorts, fn($query) => $query->first())
            ->when($this->filters['search'], function ($query, $search) {
                $query->where('session_name', 'LIKE', "%$search%");
            })->latest();

        return $this->applyPagination($query);
    }

    public function save()
    {
        $rules = [
            'sessionName' => ['required', 'string', 'min:2', 'max:255', 'unique:school_sessions,session_name,' . $this->schoolSessionId],
            'startTime' => ['required', 'date_format:H:i'],
            'endTime' => ['required', 'date_format:H:i', 'after:startTime'],
            'description' => ['nullable', 'string'],
            'statusActive' => ['boolean'],
        ];

        $this->validate($rules);

        try {
            DB::beginTransaction();

            if ($this->schoolSessionId) {
                $session = SchoolSession::findOrFail($this->schoolSessionId);
                $session->update([
                    'session_name' => $this->sessionName,
                    'start_time' => $this->startTime,
                    'end_time' => $this->endTime,
                    'description' => $this->description,
                    'status_active' => $this->statusActive,
                ]);
            } else {
                SchoolSession::create([
                    'session_name' => $this->sessionName,
                    'start_time' => $this->startTime,
                    'end_time' => $this->endTime,
                    'description' => $this->description,
                    'status_active' => $this->statusActive,
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            logger()->error(
                '[sesi sekolah] ' .
                    auth()->user()->username .
                    ' gagal menyimpan sesi',
                [$e->getMessage()]
            );

            $message = $this->schoolSessionId ? "Gagal menyunting data sesi sekolah." : "Gagal menambahkan data sesi sekolah.";

            session()->flash('alert', [
                'type' => 'danger',
                'message' => 'Gagal!',
                'detail' => $message,
            ]);

            return redirect()->back();
        }

        $message = $this->schoolSessionId ? "Berhasil menyunting data sesi sekolah." : "Berhasil menambahkan data sesi sekolah.";

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Berhasil!',
            'detail' => $message,
        ]);

        return redirect()->route('master.school-session.index');
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function muatUlang()
    {
        $this->dispatch('muat-ulang');
        $this->reset();
    }

    public function render()
    {
        return view('livewire.master.school-session.index');
    }
}

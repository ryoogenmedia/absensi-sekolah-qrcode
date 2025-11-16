<?php

namespace App\Livewire\Home;

use App\Models\User;
use Livewire\Component;

class Index extends Component
{
    public function getLoginHistories()
    {
        $user = User::query();

        if (auth()->user()->role == 'guru') {
            $user = $user->where('role', 'guru');
        }

        if (auth()->user()->role == 'siswa') {
            $user = $user->where('role', 'siswa');
        }

        $query = $user->whereNotNull('last_login_time')
            ->orderBy('last_login_time', 'DESC');

        $query = secret_user($query);

        return $query->limit(20)->get();
    }

    public function render()
    {
        return view('livewire.home.index', [
            'login_history' => $this->getLoginHistories(),
        ]);
    }
}

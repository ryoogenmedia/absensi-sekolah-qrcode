<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AdminExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::where('role', 'admin')
            ->select([
                'id',
                'username',
                'email',
                'role',
                'last_login_time',
                'last_login_ip',
                'last_seen_time'
            ])
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Username',
            'Email',
            'Role',
            'Last Login Time',
            'Last Login IP',
            'Last Seen Time'
        ];
    }
}

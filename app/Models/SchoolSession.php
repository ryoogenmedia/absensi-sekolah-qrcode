<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_name',
        'start_time',
        'end_time',
        'description',
        'status_active',
    ];

    /**
     * Get active sessions formatted exactly like legacy const configurations
     */
    public static function getActiveSessions()
    {
        $sessions = self::where('status_active', true)->orderBy('start_time')->get();
        $formatted = [];
        foreach ($sessions as $session) {
            $key = 'sesi_' . $session->id;
            $start = date('H:i', strtotime($session->start_time));
            $end = date('H:i', strtotime($session->end_time));
            $formatted[$key] = [
                'id' => $session->id,
                'label' => $session->session_name . " ({$start} - {$end})",
                'start' => $start,
                'end' => $end,
            ];
        }
        return $formatted;
    }
}

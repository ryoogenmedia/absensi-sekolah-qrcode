<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckInRecord extends Model
{
    use HasFactory;

    protected $table = 'check_in_records';

    protected $fillable = [
        'student_id',
        'check_in_time',
        'attendance_date',
        'remarks',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'check_in_time' => 'datetime:H:i:s',
        'attendance_date' => 'date',
        'remarks' => 'string',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id')->withDefault();
    }

    public function getCheckInTimeAttribute($value)
    {
        return Carbon::parse($value)->format('H:i:s');
    }

    public function getAttendanceDateAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y');
    }
}

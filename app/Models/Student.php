<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'class_room_id',
        'in_school',
        'full_name',
        'call_name',
        'sex',
        'nis',
        'phone',
        'religion',
        'origin_school',
        'birth_date',
        'place_of_birth',
        'address',
        'postal_code',
        'admission_year',
        'father_name',
        'mother_name',
        'father_job',
        'mother_job',
        'photo',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function student_attendances()
    {
        return $this->hasMany(StudentAttendance::class, 'student_id', 'id');
    }

    public function class_room()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id', 'id')->withDefault();
    }

    public function check_in_records()
    {
        return $this->hasMany(CheckInRecord::class, 'student_id', 'id');
    }

    public function check_out_records()
    {
        return $this->hasMany(CheckOutRecord::class, 'student_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }

    public function guardian()
    {
        return $this->hasOne(StudentGuardian::class, 'student_id', 'id')->withDefault();
    }

    public function getBirthDateAttribute($value)
    {
        return $value
            ? \Carbon\Carbon::parse($value)->format('Y-m-d')
            : null;
    }

    public function getAdmissionYearAttribute($value)
    {
        return $value
            ? \Carbon\Carbon::parse($value)->format('Y')
            : null;
    }

    public function photoUrl()
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('static/ryoogen/default/NO-IMAGE.png');
    }
}

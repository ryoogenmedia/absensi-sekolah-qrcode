<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'teachers';

    protected $fillable = [
        'user_id',
        'subject_study_id',
        'name',
        'sex',
        'nip',
        'nuptk',
        'phone',
        'religion',
        'birth_date',
        'place_of_birth',
        'address',
        'postal_code',
        'date_joined',
        'photo',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'admission_year' => 'year',
    ];

    public function subject_study()
    {
        return $this->belongsTo(SubjectStudy::class, 'subject_study_id', 'id')->withDefault();
    }

    public function class_schedules()
    {
        return $this->hasMany(ClassSchedule::class, 'teacher_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }

    public function getBirthDateAttribute($value)
    {
        return $value
            ? \Carbon\Carbon::parse($value)->format('Y-m-d')
            : null;
    }

    public function getDateJoinedAttribute($value)
    {
        return $value
            ? \Carbon\Carbon::parse($value)->format('Y-m-d')
            : null;
    }

    public function photoUrl()
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('static/ryoogen/default/NO-IMAGE.png');
    }
}

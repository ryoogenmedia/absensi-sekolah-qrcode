<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassAttendance extends Model
{
    use HasFactory;

    protected $table = 'class_attendances';

    protected $fillable = [
        'class_room_id',
        'class_schedule_id',
        'picture_evidence',
        'explanation_material',
        'name_material',
    ];

    protected $casts = [
        'class_room_id' => 'integer',
        'class_schedule_id' => 'integer',
        'picture_evidence' => 'string',
        'explanation_material' => 'string',
        'name_material' => 'string',
    ];

    public function student_attendances()
    {
        return $this->hasMany(StudentAttendance::class, 'class_attendance_id', 'id');
    }

    public function class_room()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id', 'id')->withDefault();
    }

    public function class_schedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id', 'id')->withDefault();
    }

    public function pictureEvidenceUrl()
    {
        return $this->picture_evidence
            ? asset('storage/' . $this->picture_evidence)
            : asset('static/ryoogen/default/NO-IMAGE.png');
    }
}

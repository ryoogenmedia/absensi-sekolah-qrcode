<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'class_rooms';

    protected $fillable = [
        'name_class',
        'description',
        'status_active',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function students(){
        return $this->hasMany(Student::class,'class_room_id','id');
    }

    public function class_advisor(){
        return $this->hasOne(ClassAdvisor::class,'class_room_id','id')->withDefault();
    }

    public function class_schedules(){
        return $this->hasMany(ClassSchedule::class,'class_room_id','id');
    }
}

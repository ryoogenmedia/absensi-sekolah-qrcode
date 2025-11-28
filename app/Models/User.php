<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'force_logout',
        'username',
        'avatar',
        'role',
        'email',
        'password',
        'email_verified_at',
        'last_login_time',
        'last_login_ip',
        'last_seen_time',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // GET AVATAR URL
    public function avatarUrl()
    {
        return $this->avatar
            ? url('storage/' . $this->avatar)
            : 'https://gravatar.com/avatar/' . md5(strtolower(trim($this->email))) . '?s=1024';
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id', 'id')->withDefault();
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id', 'id')->withDefault();
    }

    public function student_guardian()
    {
        return $this->hasOne(StudentGuardian::class, 'user_id', 'id')->withDefault();
    }

    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = ucwords(strtolower($value));
    }
}

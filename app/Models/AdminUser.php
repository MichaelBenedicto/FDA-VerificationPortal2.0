<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use Notifiable;

    protected $connection = 'dbAdmin';   // <-- use dbAdmin connection
    protected $table = 'admin_users';
    protected $primaryKey = 'idadmin_users';
    public $timestamps = false;

    protected $fillable = [
        'user_level', 'userName', 'password', 'email', 'fullName', 'center_office', 'activated'
    ];

    protected $hidden = [
        'password'
    ];

//     // Hash password automatically when creating/updating
//     public function setPasswordAttribute($password)
//     {
//         $this->attributes['password'] = bcrypt($password);
//     }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EltoVerification extends Model
{
    protected $connection = 'fdaeservices';
    protected $table = 'elto_db_verification';
    public    $timestamps = false; // if the table has no timestamps
    protected $guarded = [];
}

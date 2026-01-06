<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Huhs extends Model
{
    protected $connection = 'ccrr';
    protected $table = 'lto_huhs';
    public    $timestamps = false;
    protected $guarded = [];
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DrugProduct extends Model
{
    protected $connection = 'cdrr';
    protected $table = 'all_drugproducts';
    public    $timestamps = false;
    protected $guarded = [];
}

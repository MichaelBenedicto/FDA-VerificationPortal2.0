<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class lto_drugs extends Model
{
    protected $connection = 'lto_drugs';
    protected $table = 'drug_est_verif';
    public    $timestamps = false;
    protected $guarded = [];
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FoodProduct extends Model
{
    protected $connection = 'fdafoodproducts';
    protected $table = 'food_products';
    public    $timestamps = false;
    protected $guarded = [];
}

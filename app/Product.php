<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'short_description',
        'price',
        'pages_head',
        'pages'
    ];

    public function images()
    {
        return $this->hasMany('App\ProductImage');
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function cartItems()
    {
        return $this->hasMany('App\CartItem');
    }
}

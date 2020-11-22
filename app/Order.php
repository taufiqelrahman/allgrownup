<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $fillable = ['shopify_order_id', 'order_number', 'state_id'];
    // public function orderItems()
    // {
    //     return $this->hasMany('App\OrderItem');
    // }
    public function state()
    {
        return $this->belongsTo('App\State');
    }
    public function printings()
    {
        return $this->hasOne('App\Printing');
    }
}

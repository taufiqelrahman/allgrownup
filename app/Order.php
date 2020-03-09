<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    /*
    STATE MACHINE
    1: created (menunggu tipe pembayaran)
    2: pending (menunggu pembayaran: pending)
    3: paid (telah dibayar: settlement, capture)
    4: sent (telah dikirim, shipping_number terisi)
    5: expired (expire)
    // received (telah diterima) (need create auto-receive job)
    // canceled (dibatalkan /gagal)
    */

    use SoftDeletes;
    // protected $fillable = ['total', 'payment_type', 'shipping_method', 'shipping_rate'];
    // public function orderItems()
    // {
    //     return $this->hasMany('App\OrderItem');
    // }
}

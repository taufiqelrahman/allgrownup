<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    /*
    STATE MACHINE
    1: created (menunggu tipe pembayaran)
    2: pending (menunggu pembayaran: pending)
    3: paid (telah dibayar: settlement, capture)
    4: sent (telah dikirim, shipping_number terisi)
    5: done (selesai) (need create auto-done job based-on time)
    6: expired (expire)
    // canceled (dibatalkan /gagal)
    */
    use SoftDeletes;
    protected $fillable = ['name'];
    //
}

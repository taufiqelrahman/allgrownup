<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    /*
    STATE MACHINE
    1: created (menunggu pembayaran: pending)
    2: paid (telah dibayar: settlement, capture)
    3: sent (telah dikirim, shipping_number terisi)
    // 4: done (selesai) (need create auto-done job based-on time)
    5: expired (expire)
    6: cancelled (dibatalkan /gagal)
    7: partially-refunded (dikembalikan separuh)
    8: refunded (dikembalikan)
    */
    use SoftDeletes;
    protected $fillable = ['name'];
    //
}

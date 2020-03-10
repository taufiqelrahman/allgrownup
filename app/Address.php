<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'address1',
        'address2',
        'city',
        'province',
        'zip',
        'phone'
    ];
}

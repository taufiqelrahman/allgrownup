<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Child extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'order_id',
        'name',
        'cover',
        'gender',
        'age',
        'skin',
        'hair',
        'birthdate',
        'message',
        'language',
        'occupations'
    ];
    //
}

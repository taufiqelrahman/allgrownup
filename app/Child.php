<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use SoftDeletes;
    protected $fillable = ['order_id', 'name', 'cover', 'gender', 'age', 'skin', 'hair', 'birthdate', 'message'];
    //
}

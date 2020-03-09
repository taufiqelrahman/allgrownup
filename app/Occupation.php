<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Occupation extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description', 'page_count'];
    //
}

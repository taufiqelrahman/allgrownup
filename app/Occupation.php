<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description', 'page_count'];
    //
}

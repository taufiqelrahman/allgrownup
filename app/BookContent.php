<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookContent extends Model
{
    use SoftDeletes;
    protected $fillable = ['book_page_id', 'value', 'style'];
    //
}

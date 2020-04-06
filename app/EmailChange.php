<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailChange extends Model
{
    use SoftDeletes;
    protected $fillable = ['shopify_ordeuser_idr_id', 'email', 'token'];
}

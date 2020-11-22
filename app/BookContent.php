<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookContent extends Model
{
    use SoftDeletes;
    protected $fillable = ['page_number', 'english', 'indonesia', 'style', 'occupation_id'];

    public function occupation()
    {
        return $this->belongsTo('App\Occupation');
    }
}

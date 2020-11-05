<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Printing extends Model
{
    /**
     * 
     * PRINTING STATE MACHINE
     * 1. order confirmation
     * 2. Prepress approval
     * 3. In production
     * 4. QC
     * 5. Waiting shipment
     * 6. Done
     * 
     */
    use SoftDeletes;
    protected $fillable = [
        'order_id',
        'printing_state',
        'source_path',
        'note'
    ];
}
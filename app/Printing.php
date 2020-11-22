<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Printing extends Model
{
    /**
     * 
     * PRINTING STATE MACHINE
     * 1. Preparation PDF (Dhana)
     * 2. In progress (Dhana)
     * 3. QC PDF (Riyan)
     * 4. In progress (Riyan)
     * 5. Printing & shipping (Fariz)
     * 6. In progress (Fariz)
     * 7. Done (Input resi)
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
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Printing extends Model
{
    /**
     * 
     * PRINTING STATE MACHINE
     * - Preparation PDF
     * - In progress
     * - QC PDF
     * - In progress
     * - PDF ready
     * - Layouting
     * - Printing & Shipping
     * - Done (Input resi)
     * - Retur in progress
     * - Retur terkirim
     * 
     */
    use SoftDeletes;
    protected $fillable = [
        'order_id',
        'printing_state',
        'source_path',
        'note',
        'assignee'
    ];
}
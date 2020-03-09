<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Testimonial;

class MasterController extends Controller
{
    /**
     * Display a listing of the testimonials.
     *
     * @return \Illuminate\Http\Response
     */
    public function testimonials(Request $request)
    {
        $testimonials = Testimonial::get();
        return response(['data' => $testimonials], 200);
    }
}

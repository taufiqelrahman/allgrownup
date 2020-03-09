<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Testimonial;
use App\Occupation;

class MasterController extends Controller
{
    /**
     * Display a listing of the testimonials.
     *
     * @return \Illuminate\Http\Response
     */
    public function testimonials()
    {
        $testimonials = Testimonial::get();
        return response(['data' => $testimonials], 200);
    }

    /**
     * Display a listing of the occupations.
     *
     * @return \Illuminate\Http\Response
     */
    public function occupations()
    {
        $occupations = Occupation::get();
        return response(['data' => $occupations], 200);
    }
}

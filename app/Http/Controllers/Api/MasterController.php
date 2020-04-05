<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Testimonial;
use App\Occupation;
use App\BookPage;

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

    /**
     * Display a listing of the book pages.
     *
     * @return \Illuminate\Http\Response
     */
    public function bookPages()
    {
        $book_pages = BookPage::with('occupation')->with('bookContents')->get();
        return response(['data' => $book_pages], 200);
    }

    /**
     * Display a listing of the provinces.
     *
     * @return \Illuminate\Http\Response
     */
    public function provinces()
    {
        $data = app(ServiceController::class)->retrieveProvinces()->provinces;
        return response(['data' => $data], 200);
    }
}

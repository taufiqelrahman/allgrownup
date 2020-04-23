<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Testimonial;
use App\Occupation;
use App\BookContent;

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
        $occupations = Occupation::whereNotIn('id', [11, 12, 13, 14])->get();
        return response(['data' => $occupations], 200);
    }

    /**
     * Display a listing of the book pages.
     *
     * @return \Illuminate\Http\Response
     */
    public function bookPages(Request $request)
    {
        $ids = explode(',', $request->jobs);
        $data = array_merge(array(13, 11), $ids, array(13, 14));
        $ids_ordered = implode(',', $data);
        $book_pages = BookContent::whereIn('occupation_id', $data)
                                ->with('occupation')
                                ->orderByRaw("FIELD(occupation_id, $ids_ordered)")
                                ->get();
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

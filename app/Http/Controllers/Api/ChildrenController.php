<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ChildrenController extends Controller
{

    /**
     * Display a listing of the resource.
     * for admin dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $children = Child::get();

        return response(['data' => ['children' => $children]], 200);
    }

}

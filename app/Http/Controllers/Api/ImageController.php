<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasfile('image'))
        {
            $image = $request->file('image');
            $imageName = $image->getclientoriginalname();
            $filename = preg_replace('/[^a-zA-Z0-9_]/', '_', pathinfo($imageName, PATHINFO_FILENAME));
            $extension = pathinfo($imageName, PATHINFO_EXTENSION);
            $filepath = $image->storeAs('images', time() .'_'. $filename .'.'. $extension);
            return response(['data' => $filepath], 200);
        }
        return response(['message' => 'No file was included.'], 500);
    }
}

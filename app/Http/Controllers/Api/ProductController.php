<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Product;
use App\ProductImage;
use App\StoryPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('images')->get();
        return response(['data' => $products], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $response = \DB::transaction(function() use ($request) {
            $product = new Product;
            $data = $request->only($product->getFillable());
            $product->fill($data);
            $product->slug = Str::slug($product->name, '-');
            $product->save();
            foreach($request->images as $image)
            {
                $productImage = new ProductImage;
                $productImage->product_id = $product->id;
                $productImage->filepath = $image;
                $productImage->save();
            }
            return response(['data' => $product], 200);
        }, 5);
        
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with('images')->with('category')->findOrFail($id);
        $product->story_pages = StoryPage::whereIn('id', explode(',', $product->pages))->get();
        return response(['data' => $product], 200);
    }

    public function showSlug($slug)
    {
        $product = Product::with('images')
                    ->with('category')
                    ->where('slug', $slug)
                    ->firstOrFail();
        $product->story_pages = StoryPage::whereIn('id', explode(',', $product->pages))->get();
        return response(['data' => $product], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

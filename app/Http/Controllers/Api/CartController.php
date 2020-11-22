<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Cart;
use App\CartItem;
use App\User;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request->user());
        $cart = Cart::with('cartItems')
            ->where('user_id', $request->user()->id)
            ->first();
        return response(['data' => $cart], 200);
    }

    public function addItem(Request $request)
    {
        $response = \DB::transaction(function() use ($request) {
            $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
            $input = json_decode($request->getContent());
            $cartItem = CartItem::updateOrCreate(
                ['cart_id' => $cart->id, 'product_id' => $input->product_id],
                ['quantity' => $input->quantity, 'price' => $input->price]
            );
            return response(['data' => $cart->with('cartItems')->first()], 200);
        }, 5);
        return $response;
    }
    
    public function removeItem(Request $request)
    {
        $response = \DB::transaction(function() use ($request) {
            $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
            
            $cartItem = CartItem::where([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id
            ]);
            $cartItem->delete();
            return response(['data' => $cart->with('cartItems')->first()], 200);
        }, 5);
        return $response;
    }

    public function createCart(Request $request)
    {
        $userId = $request->user()->id;
        Cart::where('user_id', $request->user()->id)->delete();
        $cart = Cart::create([ 'user_id' => $userId, 'checkout_id' => $request->checkoutId]);
        return response(['data' => $cart], 200);
    }
}

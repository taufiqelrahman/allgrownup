<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Order;
use App\OrderItem;
use App\Address;
use App\User;
use App\Cart;
use App\CartItem;
use App\Mail\OrderCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = app(ServiceController::class)->retrieveOrders();
        $data->checkouts = app(ServiceController::class)->retrieveAbandonedCheckouts()->checkouts;
        return response(['data' => $data], 200);
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
            $user = User::find($request->user()->id);
            // get address
            if (isset($request->address_id))
            {
                $address = Address::find($request->address_id);
            } else {
                $address = new Address;
                $address->fill($request->address)->save();
                $user->address_id = $address->id;
                $user->save();
            }
            // save order
            $order = new Order;
            $order->fill($request->order); 
            $order->order_number = 'pending';
            $order->user_id = $user->id;
            $order->address_id = $address->id;
            $order->status = 1;
            $order->save();
            // update order_number
            $order->order_number = date("Y") . str_pad($order->id,8,'0',STR_PAD_LEFT);
            $order->save();
            // move cart items to order items
            $cart = Cart::where('user_id', $user->id)->firstOrFail();
            $cartItems = $cart->cartItems()->get();
            if ($cartItems->isEmpty()) return response(['message' => 'No items in cart'], 500);
            foreach($cartItems as $item)
            {
                $order_item = new OrderItem;
                $order_item->order_id = $order->id;
                $order_item->product_id = $item->product_id;
                $order_item->quantity = $item->quantity;
                $order_item->total = $item->price * $item->quantity;
                $order_item->save();
            }
            CartItem::where('cart_id', $cart->id)->delete();
            $order = $order->with('orderItems')->findOrFail($order->id);
            $redirect_url = app(MidtransController::class)->charge($order, $user);
            Mail::to($request->user())->queue(new OrderCreated($order));
            return response(
                ['data' => $order,
                'redirect_url' => $redirect_url
                ], 200);
        }, 5);

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::findOrFail($id)->with('orderItems');
        return response(['data' => $order], 200);
    }

    /**
     * Display the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showDetail($order_number)
    {
        $order = Order::where('order_number', $order_number)->with('state')->first();
        $data = app(ServiceController::class)->retrieveOrderById($order->shopify_order_id);
        $transactions = app(ServiceController::class)->retrieveTransactionById($order->shopify_order_id)->transactions;
        $data->transaction = last($transactions);
        $data->payment = app(MidtransController::class)->getTransaction($data->transaction->authorization);
        $data->state = $order->state;
        // $data = app(ServiceController::class)->retrieveOrderById(2079230722181);
        
        return response(['data' => $data], 200);
    }

    /**
     * Display the specified abandoned checkouot.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showCheckout($checkout_id)
    {
        $data = app(ServiceController::class)->retrieveAbandonedCheckouts()->checkouts;
        $checkout = collect($data)->first(function ($value, $key) use ($checkout_id) {
            return $value->id == $checkout_id;
        });
        
        return response(['data' => $checkout], 200);
    }

    /**
     * [Webhook] Create the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function webhookCreate(Request $request)
    {
        $response = \DB::transaction(function() use ($request) {
            $user = User::with('address')->where('email', $request->email)->firstOrFail();
            // save address
            if (isset($user->address))
            {
                $address = $user->address;
            } else {
                $address = new Address;
            }
            $address->fill($request->shipping_address);
            $unallowed_props = ['company', 'latitude', 'longitude', 'name', 'country_code', 'province_code'];
            foreach ($address as $key => $value) {
                if (!in_array($key, $unallowed_props)) {
                    unset($address->$key);
                }
            }
            $address->save();
            $user->address_id = $address->id;
            $user->save();
            // save order
            $order = new Order;
            $order->shopify_order_id = $request->id;
            $order->user_id = $user->id;
            $order->state_id = 1;
            $order->order_number = str_replace('#', '', $request->name);
            $order->save();
            // delete cart
            $cart = Cart::where('user_id', $user->id)->firstOrFail();
            $cart->delete();
            Mail::to($request->user())->queue(new OrderCreated($order));
            return response(['data' => $order], 200);
        }, 5);

        return $response;
    }

    /**
     * [Webhook] Update the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function webhookPaid(Request $request)
    {
        $response = \DB::transaction(function() use ($request) {
            $order = Order::where('order_number', 'WIGU'.$request->order_number)->firstOrFail();
            $order->status = 2;
            $order->save();
            return response(['data' => $order], 200);
        }, 5);

        return $response;
    }

    /**
     * [Webhook] Update the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function webhookSent(Request $request)
    {
        $response = \DB::transaction(function() use ($request) {
            $order = Order::where('order_number', 'WIGU'.$request->order_number)->firstOrFail();
            $order->status = 3;
            $order->save();
            return response(['data' => $order], 200);
        }, 5);

        return $response;
    }

    /**
     * [Webhook] Update the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function webhookCancelled(Request $request)
    {
        $response = \DB::transaction(function() use ($request) {
            $order = Order::where('order_number', 'WIGU'.$request->order_number)->firstOrFail();
            $order->status = 6;
            $order->save();
            return response(['data' => $order], 200);
        }, 5);

        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

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
    protected $guzzle;
    public function __construct()
    {
        $this->guzzle = new \GuzzleHttp\Client([
            'base_uri' => 'https://elrahman.myshopify.com',
            'headers' => ['X-Shopify-Access-Token' => '944c15a82bd703a3483a6ba91ea6c6e4'],
        ]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $orders = Order::where('user_id', $request->user()->id)->get();
        $data = array();
        // foreach($orders as $order)
        // {
            // $response = $client->get('https://elrahman.myshopify.com/admin/api/2019-10/orders/' . $order->id . '.json');
            $response = $this->guzzle->get('/admin/api/2019-10/orders/1927926055052.json');
            array_push($data, json_decode($response->getBody()->getContents())->order);
        // }
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

    public function showDetail($order_number)
    {
        $order = Order::where('order_number', $order_number)->with('state')->first();
        $data = app(ServiceController::class)->retrieveOrderById($order->shopify_order_id)->order;
        $data->state = $order->state;
        // $data = app(ServiceController::class)->retrieveOrderById(2079230722181);
        
        return response(['data' => $data], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $response = \DB::transaction(function() use ($request) {
            $order = Order::where('order_number', $request->order_id)->first();
            switch($request->transaction_status)
            {
                case 'pending';
                    $order->status = 2;
                    $order->payment_type = $request->payment_type;
                    break;
                case 'settlement';
                case 'capture';
                    $order->status = 3;
                    break;
                case 'sent';
                    $order->status = 4;
                    $order->shipping_number = $request->shipping_number;
                    break;
                case 'expire';
                    $order->status = 5;
                    break;
                default;
                    break;
            }
            $order->save();
            return response(['data' => $order->with('orderItems')->findOrFail($order->id)], 200);
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

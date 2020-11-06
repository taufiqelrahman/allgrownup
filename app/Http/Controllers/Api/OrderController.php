<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Order;
use App\OrderItem;
use App\Child;
use App\Address;
use App\User;
use App\Guest;
use App\Cart;
use App\CartItem;
use App\Printing;
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
        $user_email = $request->user()->email;
        $allOrders = app(ServiceController::class)->retrieveOrders()->orders;
        // $orders = array_values(array_filter($allOrders, function ($order) use($user_email) {
        //     return ($order->email == $user_email);
        // }));
        $orders = [];
        foreach ($allOrders as $order) {
            if ($order->email == $user_email) {
                array_push($orders, $order);
            }
        }
        $order_states = Order::with('state')->get();
        // $data->checkouts = app(ServiceController::class)->retrieveAbandonedCheckouts()->checkouts;
        return response(['data' => ['orders' => $orders, 'order_states' => $order_states]], 200);
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
    public function showDetail(Request $request, $order_number)
    {
        $user_id = $request->user()->id;
        $order = Order::where('user_id', $user_id)
                        ->where('order_number', $order_number)
                        ->with('state')
                        ->firstOrFail();
        $data = app(ServiceController::class)->retrieveOrderById($order->shopify_order_id);
        $transactions = app(ServiceController::class)->retrieveTransactionById($order->shopify_order_id)->transactions;
        $last_transaction = last($transactions);
        try {
            $data->payment = app(MidtransController::class)->getTransaction($last_transaction->authorization);
        } catch (\Exception $e) { }

        $data->state = $order->state;
        // $data = app(ServiceController::class)->retrieveOrderById(2079230722181);
        
        return response(['data' => $data], 200);
    }

    /**
     * Display the specified order for guest.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showGuestDetail($order_number)
    {
        $order = Order::whereNull('user_id')
                        ->where('order_number', $order_number)
                        ->with('state')
                        ->firstOrFail();
        $data = app(ServiceController::class)->retrieveOrderById($order->shopify_order_id);
        $transactions = app(ServiceController::class)->retrieveTransactionById($order->shopify_order_id)->transactions;
        $last_transaction = last($transactions);
        try {
            $data->payment = app(MidtransController::class)->getTransaction($last_transaction->authorization);
        } catch (\Exception $e) { }
        $data->state = $order->state;
        
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
        $request = json_decode($request->getContent());
        $response = \DB::transaction(function() use ($request) {
            $isGuest = false;
            $user = User::with('address')->where('email', $request->email)->first();
            if (!isset($user)) {
                $user = new Guest;
                $user->name = $request->shipping_address->name;
                $user->email = $request->email;
                $user->phone = $request->shipping_address->phone;
                $user->save();
                $isGuest = true;
            }
            // save address
            if (isset($user->address))
            {
                $address = $user->address;
            } else {
                $address = new Address;
            }
            $shipping_address = $request->shipping_address;
            $unallowed_props = ['company', 'latitude', 'longitude', 'name', 'country_code', 'province_code'];
            foreach ($shipping_address as $key => $value) {
                if (in_array($key, $unallowed_props)) {
                    unset($shipping_address->$key);
                }
            }
            $address->fill((array) $shipping_address);
            $address->save();
            $user->address_id = $address->id;
            $user->save();
            // save order
            $order = new Order;
            $order->shopify_order_id = $request->id;
            if ($isGuest) {
                $order->guest_id = $user->id;
            } else {
                $order->user_id = $user->id;
            }
            $order->state_id = 1;
            $order->order_number = str_replace('#', '', $request->name);
            $order->save();
            // save child data
            $shopify_data = app(ServiceController::class)->retrieveOrderById($order->shopify_order_id)->order;
            foreach ($shopify_data->line_items as $data) {
                $child_data = (object)[];
                foreach ($data->properties as $prop)
                {
                    $child_data->{$prop->name} = $prop->value;
                }
                $child = new Child;
                $child->order_id = $order->id;
                $child->name = $child_data->Name;
                $child->cover = $child_data->Cover;
                $child->gender = $child_data->Gender;
                $child->age = $child_data->Age;
                $child->skin = $child_data->Skin;
                $child->hair = $child_data->Hair;
                // $child->birthdate = $child_data->{'Date of Birth'};
                $child->message = $child_data->Dedication;
                $child->language = $child_data->Language;
                $child->occupations = $child_data->Occupations;
                $child->save();
            }
            // delete cart
            $cart = Cart::where('user_id', $user->id)->delete();
            // $cart->delete();
            // Mail::to($user)->queue(new OrderCreated($order));
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
        $request = json_decode($request->getContent());
        $response = \DB::transaction(function() use ($request) {
            $order = Order::where('order_number', 'WIGU-'.$request->order_number)->firstOrFail();
            $order->state_id = 2;
            $order->save();

            // create printing object with order_id
            $printing = new Printing;
            $printing->order_id = $order->id;
            $printing->printing_state = 'Order Confirmation';
            $printing->save();
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
        $request = json_decode($request->getContent());
        $response = \DB::transaction(function() use ($request) {
            $order = Order::where('order_number', 'WIGU-'.$request->order_number)->firstOrFail();
            $order->state_id = 3;
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
        $request = json_decode($request->getContent());
        $response = \DB::transaction(function() use ($request) {
            $order = Order::where('shopify_order_id', $request->id)->firstOrFail();
            $order->state_id = 6;
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
    public function webhookRefunded(Request $request)
    {
        $request = json_decode($request->getContent());
        $response = \DB::transaction(function() use ($request) {
            $order = Order::where('shopify_order_id', $request->id)->firstOrFail();
            if ($request->financial_status == 'partially_refunded') {
                $order->state_id = 7;
            } else {
                $order->state_id = 8;
            }
            $order->save();
            return response(['data' => $order], 200);
        }, 5);

        return $response;
    }

    /**
     * Display a listing of the resource.
     * for admin dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $user_email = $request->user()->email;
        $allOrders = app(ServiceController::class)->retrieveOrders()->orders;
        $orders = [];
        foreach ($allOrders as $order) {
            if ($order->financial_status == 'paid') {
                array_push($orders, $order);
            }
        }
        $order_printing = Order::with('printings')
                            ->has('printings')
                            ->get();

        return response(['data' => ['orders' => $orders, 'order_printing' => $order_printing]], 200);
    }

    /**
     * Update printing
     * for admin dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePrinting ($id, Request $request)
    {
        $isAdmin = $request->user()->is_admin;
        if ($isAdmin) {
            $printing = Printing::where('order_id', $id)->firstOrFail();
            $printing->printing_state = $request->status;
            $printing->source_path = $request->path;
            $printing->save();
            return response($printing, 200);
        }
        return response('Unauthorized', 401);
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

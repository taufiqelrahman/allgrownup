<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Address;
use App\Product;

class MidtransController extends Controller
{
    public function __construct()
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        // \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        // \Midtrans\Config::$is3ds = true;
    }

    public function charge($order, $user)
    {
        $transaction_details = array(
            'order_id'      => $order->order_number,
            'gross_amount'  => $order->total
        );

        // Populate items
        // $items = [];
        // foreach($order->orderItems as $item)
        // {
        //     $product = Product::findOrFail($item->product_id);
        //     array_push($items, array(
        //         'id'                => 'item1',
        //         'price'         => $product->price,
        //         'quantity'  => $item->quantity,
        //         'name'          => $product->name
        //     ));
        // }

        $userAddress = Address::findOrFail($user->address_id);
        $name = explode(' ', trim($user->name));
        $address = array(
            'first_name'   => $name[0],
            'last_name'    => count($name) > 1 ? $name[count($name)-1] : '',
            'address'      => $userAddress->address,
            'city'         => $userAddress->city,
            'postal_code'  => $userAddress->post_code,
            'phone'        => $userAddress->phone,
        );

        // Populate customer's info
        $customer_details = array(
            'first_name'       => $name[0],
            'last_name'        => count($name) > 1 ? $name[count($name)-1] : '',
            'name'             => $user->name,
            'email'            => $user->email,
            'phone'            => $userAddress->phone,
            'billing_address'  => $address,
            'shipping_address' => $address
        );

        // Data yang akan dikirim untuk request redirect_url.
        $transaction_data = array(
            'transaction_details'   => $transaction_details,
            // 'item_details'           => $items,
            'customer_details'      => $customer_details
        );

        try
        {
            $snapTransaction = \Midtrans\Snap::createTransaction($transaction_data);
            // $token = snapTransaction->token;
            return $snapTransaction->redirect_url;
        } 
        catch (Exception $e) 
        {   
            return $e->getMessage;
        }
    }

    public function getTransaction($order_number)
    {
        $status = \Midtrans\Transaction::status(201900000068);
        return $status;
    }
}

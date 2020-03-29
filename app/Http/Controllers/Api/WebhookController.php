<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function ordersCreate(Request $request)
    {  
        return app(OrderController::class)->webhookCreate($request);
    }

    public function ordersPaid(Request $request)
    {  
        return app(OrderController::class)->webhookPaid($request);
    }

    public function ordersSent(Request $request)
    {  
        return app(OrderController::class)->webhookSent($request);
    }

    public function ordersCancelled(Request $request)
    {  
        return app(OrderController::class)->webhookCancelled($request);
    }
}

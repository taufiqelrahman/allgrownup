<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Message;

class MessageController extends Controller
{
    public function saveMessage(Request $request)
    {
        $response = \DB::transaction(function() use ($request) {
            $message = new Message;
            $message->email = $request->email;
            $message->message = $request->message;
            if (isset($request->userId)) {
                $message->user_id = $request->userId;
            }
            $message->save();
            return response(['data' => $message], 200);
        }, 5);
        return $response;
    }
}

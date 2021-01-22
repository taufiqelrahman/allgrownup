<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['json.response']], function () {

    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });

    // public routes
    Route::post('/login', 'Api\AuthController@login')->name('login.api');
    Route::post('/login-facebook', 'SocialAuthController@callbackFacebook')->name('login.facebook.api');
    Route::post('/login-google', 'SocialAuthController@callbackGoogle')->name('login.google.api');
    Route::post('/register', 'Api\AuthController@register')->name('register.api');
    Route::post('/check-email', 'Api\AuthController@checkEmailExists')->name('check.email.api');
    Route::post('/forgot-password', 'Api\AuthController@forgotPassword')->name('forgot.password.api');
    Route::post('/reset-password', 'Api\AuthController@resetPassword')->name('reset.password.api');
    Route::get('/confirm-email-change', 'Api\AuthController@confirmEmailChange')->name('email.change.api');
    
    Route::get('/testimonials', 'Api\MasterController@testimonials')->name('testimonial.api');
    Route::get('/occupations', 'Api\MasterController@occupations')->name('occupation.api');
    Route::get('/provinces', 'Api\MasterController@provinces')->name('province.api');
    Route::get('/book-pages', 'Api\MasterController@bookPages')->name('book.pages.api');
    
    Route::post('/message/send', 'Api\MessageController@saveMessage')->name('save.message');
    
    // private routes
    Route::middleware('auth:api')->group(function () {
        
        Route::get('/logout', 'Api\AuthController@logout')->name('logout');
        Route::get('/me', 'Api\AuthController@me')->name('me');
        Route::post('/me', 'Api\AuthController@updateMe')->name('update.me');
        Route::post('/check-email-change', 'Api\AuthController@checkEmailExists')->name('change.email.api');
        
        Route::post('/send-otp', 'Api\OtpController@send')->name('send.otp.api');
        Route::get('/is-admin', 'Api\AuthController@isAdmin')->name('check.admin.api');
        Route::get('/users', 'Api\AuthController@users')->name('users.admin.api');
        Route::patch('/users/{id}', 'Api\AuthController@updateUser')->name('update.user.admin.api');

        Route::post('/cart', 'Api\CartController@createCart')->name('cart.create');
        // Route::get('/cart', 'Api\CartController@index')->name('cart.index');
        // Route::post('/cart', 'Api\CartController@addItem')->name('cart.add');
        // Route::delete('/cart', 'Api\CartController@removeItem')->name('cart.remove');
        
        // Route::post('/images/upload', 'Api\ImageController@upload');

        Route::apiResources(['orders' => 'Api\OrderController']);
        Route::get('orders/{order_number}/detail', 'Api\OrderController@showDetail')->name('order.showDetail');
        // Route::get('orders/{checkout_id}/checkout', 'Api\OrderController@showCheckout')->name('order.showCheckout');
        
        /**
         * Dashboard Routes
         */
        Route::get('children', 'Api\ChildrenController@list')->name('children.list');

        Route::get('orderslist', 'Api\OrderController@list')->name('order.list');
        Route::patch('orderslist/{id}', 'Api\OrderController@updatePrinting')->name('update.printing');
        Route::post('orders/{id}/fulfill', 'Api\OrderController@fulfill')->name('order.fulfill');
        Route::put('orders/{id}/fulfillment/{fulfillmentId}', 'Api\OrderController@updateFulfillment')->name('order.update.fulfillment');
    });
    Route::get('orders/{order_number}/guest', 'Api\OrderController@showGuestDetail')->name('order.guestDetail');

    // Route::get('products/{slug}/slug', 'Api\ProductController@showSlug')->name('product.showSlug');
    // Route::apiResources([
    //     'products' => 'Api\ProductController',
    // ]);

    Route::post('webhook/orders/create', 'Api\WebhookController@ordersCreate');
    Route::post('webhook/orders/paid', 'Api\WebhookController@ordersPaid');
    Route::post('webhook/orders/sent', 'Api\WebhookController@ordersSent');
    Route::post('webhook/orders/cancelled', 'Api\WebhookController@ordersCancelled');
    Route::post('webhook/orders/refunded', 'Api\WebhookController@ordersRefunded');
});

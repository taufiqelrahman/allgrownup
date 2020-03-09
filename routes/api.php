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
    Route::post('/register', 'Api\AuthController@register')->name('register.api');
    Route::post('/forgot-password', 'Api\AuthController@forgotPassword')->name('forgot.password.api');
    Route::post('/reset-password', 'Api\AuthController@resetPassword')->name('reset.password.api');
    
    Route::get('/testimonials', 'Api\MasterController@testimonials')->name('testimonial.api');
    Route::get('/occupations', 'Api\MasterController@occupations')->name('occupation.api');

    // private routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/logout', 'Api\AuthController@logout')->name('logout');
        
        Route::get('/cart', 'Api\CartController@index')->name('cart.index');
        Route::post('/cart', 'Api\CartController@addItem')->name('cart.add');
        Route::delete('/cart', 'Api\CartController@removeItem')->name('cart.remove');
        
        Route::post('/images/upload', 'Api\ImageController@upload');

        Route::apiResources(['orders' => 'Api\OrderController']);
        Route::get('orders/{order_number}/detail', 'Api\OrderController@showDetail')->name('order.showDetail');
        
    });

    Route::get('products/{slug}/slug', 'Api\ProductController@showSlug')->name('product.showSlug');
    Route::apiResources([
        'products' => 'Api\ProductController',
    ]);

    Route::post('webhook', 'Api\WebhookController@handle');

});

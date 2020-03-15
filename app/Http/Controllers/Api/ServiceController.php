<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
  protected $guzzle;
  protected $ADMIN_API_PATH;
  public function __construct()
  {
    $this->guzzle = new \GuzzleHttp\Client([
        'base_uri' => env('SHOPIFY_URL'),
        'headers' => ['X-Shopify-Access-Token' => env('SHOPIFY_ACCESS_TOKEN')],
    ]);
    $this->ADMIN_API_PATH = env('ADMIN_API_PATH');
  }

  // public function createCheckout()
  // {
  //   $response = $this->guzzle->post($this->ADMIN_API_PATH.'/checkouts.json');
  //   return json_decode($response->getBody()->getContents());
  // }
}

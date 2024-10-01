<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function index(){

        return Courier::all();
    }

    public function getRajaongkirCost(Request $request){

        $client = new Client([
            'verify' => false,
        ]);
        try {
            $response = $client->request('POST', 'https://api.rajaongkir.com/starter/cost', [
                'form_params' => [
                    'origin' => '23', 
                    'destination' => $request->city_id,
                    'weight' => $request->weight,
                    'courier' => $request->courier,
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'key' => config('rajaongkir.api_key'),
                ],
            ]);
            $body = $response->getBody();
            $data = json_decode($body);
            return $data->rajaongkir->results[0];
        } catch (Exception $e) {
            return $e->getMessage();
        }
        
    }
}

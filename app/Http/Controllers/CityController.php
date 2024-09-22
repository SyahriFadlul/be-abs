<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::all();

        return response($cities);
    }

    public function provinceCities(Request $request)
    {        
        $provinceId = intval($request->input('province_id'));
        $cityByProvince = City::where('province_id', $provinceId)->get();

        return response($cityByProvince);
    }
}

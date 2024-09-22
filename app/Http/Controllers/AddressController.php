<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressCollection;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->user()->id;
        $address = Address::where('user_id', $userId)->with(['province','city'])->get();        
        return new AddressCollection($address);
    }

    public function showUserAddresses($id) 
    {
        $address = Address::where('user_id', $id)->with(['province','city'])->get();
        return new AddressCollection($address);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddressRequest $request)
    {
        $userId = auth()->user()->id;
        if ($userId === $request['user_id']) {
            $data = $request->validated();
            Address::create($data);
            return response('sukses', 200);
        } else {
            return response('Unauthorized', 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($user)
    {
        $user = Address::find($user);
        return new AddressResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AddressRequest $request, Address $address)
    {
        $data = $request->validated();        
        $data['isMainAddress'] === true ? 1 : 0;
        
        $address->update($data);
        
        return new AddressResource($address);
    }

    public function setMainAddress(AddressRequest $request, $id)
    {        
        $data = $request->validated();
        $data['isMainAddress'] = $data['isMainAddress'] === true ? 1 : 0; //konvert true false jadi 1 0
        $address = Address::find($id);
        
        $addresses = Address::where('user_id', $data['user_id'])->get();
        foreach ($addresses as $item) { // set semua ismainaddress column current user jadi false
            $index = Address::find($item->id);
            $index->update(['isMainAddress' => 0]);
        }
        $address->update($data); // set address yang dipilih jadi mainaddress
        return \response($address);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // return response($id);
        $this->authorize('delete', Address::find($id));
        $address = Address::find($id);
        $address->delete();
        
        return response()->json("Deleted succesfully");
        
    }

    // public function assignProvinceToDB()
    // {
    //     $curl = curl_init();
        
    //     $url1 = "https://api.rajaongkir.com/starter/province";
    //     $url2 = "https://api.rajaongkir.com/starter/city";

    //     $response1 = Http::withOptions([
    //         'verify' => false,
    //     ])->withHeaders([
    //         'key' => config('rajaongkir.api_key')
    //     ])->get($url1);

    //     $response2 = Http::withOptions([
    //         'verify' => false,
    //     ])->withHeaders([
    //         'key' => config('rajaongkir.api_key')
    //     ])->get($url2);

    //     $data1 = $response1['rajaongkir']['results'];
    //     foreach ($data1 as $item) {
    //         Province::create([
    //             'id' => $item['province_id'],
    //             'name' => $item['province']
    //         ]);
    //     }

    //     $data2 = $response2['rajaongkir']['results'];
    //     foreach ($data2 as $item) {
    //         City::create([
    //             'id' => $item['city_id'],
    //             'province_id' => $item['province_id'],
    //             'type' => $item['type'],
    //             'name' => $item['city_name'],
    //             'postal_code' => $item['postal_code'],
    //         ]);
    //     }
    // }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Http\Resources\CartCollection;
use Illuminate\Http\Request;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Laravel\Prompts\Prompt;

class CartController extends Controller
{
    public function index (){       
        $userId = \auth()->user()->id;
        $userCart = Cart::where('user_id', $userId)->get();        

        $cart = [];

        foreach ($userCart as $item) {
            $key = $item->user_id . '_' . $item->product_id;
            // $key = $item->id;
        

            if (!isset($cart[$key])) {
                $cart[$key] = [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'product_id' => Product::find($item->product_id),
                    'qty' => 0,
                    'selected' => $item->selected === 0 ? false : true,
                    'note' => $item->note
                ];
            }
            // \dd($cart[$key]['qty']+= $item->qty);
            $cart[$key]['qty'] += $item->qty;
        }
        
        $data = \collect((\array_values($cart)));

        return new CartCollection($data);
    }
        
    public function show($userId){
        $user = auth()->user();
        
        if ($user->id === intval($userId))  {
            $cartItems = Cart::where('user_id', $userId)->get();
            
        }else{
            return response('Unauthorized action', 401);
        }
        
        
        // Creating an empty collection to store the modified data
        $cartResourceData = collect();
        
        
        // Grouping and summing the quantities manually
    
        $groupedItems = [];
        foreach ($cartItems as $item) {
            $key = $item->user_id . '_' . $item->product_id;
            // $key = $item->id;
        

            if (!isset($groupedItems[$key])) {
                $groupedItems[$key] = [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'product_id' => Product::find($item->product_id),
                    'qty' => 0,
                    'selected' => $item->selected === 0 ? false : true,
                    'note' => $item->note
                ];
            }
            // \dd($groupedItems[$key]['qty']+= $item->qty);
            $groupedItems[$key]['qty'] += $item->qty;
        }
        
        // Convert grouped items to a collection
        $cartResourceData = collect(array_values($groupedItems));
        
        // Transforming the calculated data to CartResource
        $cartResources = CartResource::collection($cartResourceData);                        
        
        return $cartResources;                            
    }

    public function store(CartRequest $request) //create
    
    {   //data baru/request dicheck dulu
        $data = $request->validated();
        
        //cek data yang baru masuk udah ada di db atau belum
        $sameCart = Cart::where('user_id', $data['user_id'])->where('product_id', $data['product_id'])->first();
        if ($sameCart === null) {
            //menambahkan data baru ke db
            Cart::create($data);
            return \response("cart baru dibuat");
        }
        else{
            //update data yang udah ada di db
            $this->updateCart($request, $sameCart);
            
        }        
    }

    public function update(CartRequest $request, Cart $cart)
    {
        $this->authorize('update', $cart);   
        $data = $request->validated();
        $cart->update($data);
        
        return \response($cart);
    }

    public function selectingItem(Request $request,$id)
    {
        $data = $request->validate(['selected'=>['required']]);
        $value = $data['selected'] === true ? 1 : 0;
        $cart = Cart::where('user_id', $id)->update(['selected' => $value]);
        return \response($value);
    }

    public function moveItemToWishlist(Request $request)
    {        
        $currentUserId = auth()->user()->id;
        $requestUserId = $request['user_id'];
        $productId = $request['product_id'];

        if ($currentUserId !== $requestUserId) {
            return \response('Unauthorized action', 401);
        }
        
        $newWishlistItem = [
            'product_id' => $productId,
            'user_id' => $currentUserId,
        ];
        // buat/masukin product ke cart
        Wishlist::create($newWishlistItem);

        // delete item yang sudah dipindahkan dari Wishlist ke cart                
        $cartItem = Cart::find($request->id);
        $cartItem->delete();

        return \response("item has been moved to wishlist", 200);
    }
    
    public function destroy($id)
    {    
        $this->authorize('delete', Cart::find($id));
        $item = Cart::find($id);
        $item->delete();
        
        return response()->json("Deleted succesfully");
    }

    private function updateCart(CartRequest $request, Cart $cart)
    {   
        $data = $request->validated();
        $data['qty'] = $cart['qty'] + $request['qty'];
        $cart->update($data);

        return \response()->json($cart);
    }

}

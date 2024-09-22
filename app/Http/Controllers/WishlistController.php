<?php

namespace App\Http\Controllers;


use App\Http\Requests\WishlistRequest;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Wishlist;
use Illuminate\Http\Request;


class WishlistController extends Controller
{
    public function index()
    {
        $userId = \auth()->user()->id;
        $items = Wishlist::where('user_id', $userId)->get();

        $wishlists = [];
        foreach ($items as $item) {
            $key = $item->user_id . "_" . $item->product_id;

            if (!isset($wishlists[$key])) {
                $wishlists[$key] = [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'product_id' => Product::find($item->product_id),                    
                ];
            }
        };
        $wishlistCollection = \collect(array_values($wishlists));

        return $wishlistCollection;
    }

    public function store(WishlistRequest $request)
    {   
        $data = $request->validated();
        $wishlist = Wishlist::create($data);

        return \response('item has been added to wishlist', 200);
    }

    public function update(WishlistRequest $request, Wishlist $wishlist)
    {
        $data = $request->validated();
        $wishlist->update($data);

        return \response('item has been updated', 200);
    }

    public function moveItemToCart(Request $request)
    {       
        $currentUserId = auth()->user()->id;
        $requestUserId = $request['user_id'];
        $productId = $request['product_id'];

        if ($currentUserId !== $requestUserId) {
            return \response('Unauthorized action', 401);
        }
        
        $newCartItem = [
            'product_id' => $productId,
            'user_id' => $currentUserId,
            'qty' => 1,
            'note' => '',
            'selected' => false
        ];
        // buat/masukin product ke cart
        Cart::create($newCartItem);

        // delete item yang sudah dipindahkan dari Wishlist ke cart                
        $wishlistItem = Wishlist::find($request->id);
        $wishlistItem->delete();

        return \response("item has been moved to cart", 200);

    }

    public function destroy($id)
    {
        $item = Wishlist::find($id);
        $item->delete();
    }

}

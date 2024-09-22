<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
        // $groupedCartItems = $this->collection->groupBy('product_id')->map(function ($items) {
        //     return [
        //         'id' => $items->first()->id,
        //         'user_id' => $items->first()->user_id,
        //         'product_id' => $items->first()->product_id,
        //         'total_qty' => $items->sum('qty'), // Summing up the quantities
        //     ];
        // });

        // return $groupedCartItems->values();
    }
    public static function groupByUserAndProduct($cartItems)
    {
        $groupedItems = [];
        foreach ($cartItems as $item) {
            $key = $item->user_id . '_' . $item->product_id;

            if (!isset($groupedItems[$key])) {
                $groupedItems[$key] = [
                    'user_id' => $item->user_id,
                    'product_id' => $item->product_id,
                    'qty' => 0,
                ];
            }

            $groupedItems[$key]['qty'] += $item->qty;
        }

        return collect(array_values($groupedItems));
        //$cartItems = Cart::where('user_id', $userId)->get();

        // Creating an empty collection to store the modified data
        $cartResourceData = collect();

        // Grouping and summing the quantities manually
        $groupedItems = [];
        foreach ($cartItems as $item) {
            $key = $item->user_id . '_' . $item->product_id;

            if (!isset($groupedItems[$key])) {
                $groupedItems[$key] = [
                    'user_id' => $item->user_id,
                    'product_id' => $item->product_id,
                    'qty' => 0,
                ];
            }

            $groupedItems[$key]['qty'] += $item->qty;
        }

        // Convert grouped items to a collection
        $cartResourceData = collect(array_values($groupedItems));

        // Transforming the calculated data to CartResource
        $cartResources = CartResource::collection($cartResourceData);

        return $cartResources;    
                
    }
}

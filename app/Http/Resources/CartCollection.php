<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CartCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        

        return parent::toArray($request);
        // return [
        //     // 'id'=>$this->id,
        //     'user_id'=>$this->user_id,
        //     'product_id'=>$this->product_id,
        //     'qty'=>$this->qty,
        //     'note'=>$this->note,
        //     'selected'=>$this->selected,
            
        // ];
        
    }
}

<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
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
        //     'data' => $this->collection->toArray(),
        //     'pagination' => [
        //         'total' => $this->total(),
        //         'per_page' => $this->perPage(),
        //         'current_page' => $this->currentPage(),
        //         'last_page' => $this->lastPage(),
        //         'from' => $this->firstItem(),
        //         'to' => $this->lastItem(),
        //     ],
        // ];
    }
}

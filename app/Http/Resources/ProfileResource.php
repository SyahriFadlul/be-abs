<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);

        // return [
        //     'user_id'=>$this->user_id,
        //     'full_name'=>$this->full_name,
        //     'gender'=>$this->gender,
        //     'birthday'=>$this->birthday,
        //     'city'=>$this->city,
        //     'province'=>$this->province,
        //     'postal_code'=>$this->postal_code,
        //     'country'=>$this->country,
        // ];
    }
}

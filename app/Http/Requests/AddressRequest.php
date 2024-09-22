<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id'=>'required',
            'name'=>'required',
            'phone_number'=>'required',
            'address_label'=>'required',
            'address'=>'required',
            'city_id'=>'required',
            'province_id'=>'required',        
            'detail'=>'nullable',
            'is_main_address' => 'boolean',
            'province_id' => 'required',
            'postal_code' => 'required'
        ];
    }
}

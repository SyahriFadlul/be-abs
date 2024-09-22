<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Whoops\Run;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if($this->has('image') && is_string($this->input('image'))){
            $this->request->remove('image');
        }        
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' =>['required', Rule::unique('products')->ignore($this->product)],
            'code' => ['required', Rule::unique('products')->ignore($this->product)],
            'id_category' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
            'weight' => ['required', 'numeric'],
            'stock' => ['required', 'numeric'],            
            'image' => ['nullable','image'],            
            'description' => ['required'],
        ];
    }
}

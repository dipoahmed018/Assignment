<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->product);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'string|min:5|max:250',
            'description' => 'string|min:20|max:500',
            'price' => 'integer|min:1|max:10000',
            'image' => 'required|mimes:jpg,png,jpeg',
        ];
    }
}

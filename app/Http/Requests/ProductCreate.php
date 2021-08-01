<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductCreate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'string|required|min:5|max:250',
            'description' => 'string|required|min:20|max:1500',
            'price' => 'integer|required|min:1|max:10000',
            'image' => 'required|mimes:jpg,png,jpeg|max:20000',
        ];
    }
}

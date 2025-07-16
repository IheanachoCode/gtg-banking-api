<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    public function authorize()
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
            'product_name' => 'required|string',
            'item_code' => 'nullable|string',
            'description' => 'nullable|string',
            'account_no' => 'required|string',
            'Qty' => 'required|integer|min:1',
            'price' => 'nullable|numeric',
            'total' => 'nullable|numeric',
        ];

    }
}

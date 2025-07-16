<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_name' => 'required|string',
            'item_code' => 'required|string',
            'description' => 'required|string',
            'account_no' => 'required|string|exists:account_number,account_no',
            'Qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0'
        ];
    }
}

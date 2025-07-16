<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemDetailsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'item_code' => 'required|string|exists:item_name,item_code'
        ];
    }
}

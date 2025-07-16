<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchLgaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'state' => 'required|string|exists:state_lga,state'
        ];
    }
}
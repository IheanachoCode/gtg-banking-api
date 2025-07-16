<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RateAccountOfficerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'overall_satisfaction' => 'required|integer|between:1,5',
            'professionalism' => 'required|integer|between:1,5',
            'knowledge' => 'required|integer|between:1,5',
            'takes_ownership' => 'required|integer|between:1,5',
            'understands_myneeds' => 'required|integer|between:1,5',
            'comments' => 'required|string',
            'rated_by' => 'required|string'
        ];
    }
}

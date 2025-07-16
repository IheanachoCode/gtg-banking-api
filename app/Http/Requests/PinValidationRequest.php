<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PinValidationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|string',
            'pin_no' => 'required|string',
        ];
    }


        protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->input('userID', $this->input('user_id')),
            'pin_no' => $this->input('Pin_no', $this->input('pin_no')),
        ]);
    }


}
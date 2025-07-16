<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffPinValidationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'staff_id' => 'required|string',
            'pin_no' => 'required|string',
        ];
    }

        protected function prepareForValidation()
    {
        $this->merge([
            'staff_id' => $this->input('staffID', $this->input('staff_id')),
            'pin_no' => $this->input('Pin_no', $this->input('pin_no')),
        ]);
    }



}
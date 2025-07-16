<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePinRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone_number' => 'required|string|exists:client_registrations,phone',
            'account_no' => 'required|string|exists:account_number,account_no',
            'secret_question' => 'required|string',
            'secret_answer' => 'required|string',
            'oldPin' => 'required|string|size:4',
            'new_pin' => 'required|string|size:4',
            'retype_pin' => 'required|string|same:new_pin'
        ];
    }
}
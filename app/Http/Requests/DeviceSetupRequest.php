<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviceSetupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'setup_type' => 'required|in:New,Transfer,Lost Device',
            'device_series_no' => 'required|string',
            'phone_number' => 'required|string',
            'user_id' => 'required|string|exists:client_registrations,user_id',
            'account_no' => 'required|string|exists:account_number,account_no',
            'secret_question' => 'required|string',
            'secret_answer' => 'required|string',
            'password' => 'required|string|min:6',
            'retype_password' => 'required|string|same:password'
        ];
    }
}
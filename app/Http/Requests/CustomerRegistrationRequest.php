<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRegistrationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'lastname' => 'required|string|max:255',
            'othername' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'Nationality' => 'required|string',
            'birthday' => 'required|date',
            'occupation' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'residential_address' => 'required|string',
            'residential_state' => 'required|string',
            'residential_Local_govt' => 'required|string',
            'state_of_origin' => 'required|string',
            'local_govt_of_origin' => 'required|string',
            'town_of_origin' => 'required|string',
            'bvn_no' => 'required|string',
            'marital_status' => 'required|string',
            'account_type' => 'required|string',
            'means_of_identification' => 'required|string',
            'identification_no' => 'required|string',
            'staffID_get' => 'required|string',
            'next_of_kin_name' => 'required|string',
            'next_of_kin_othernames' => 'required|string',
            'next_of_kin_address' => 'required|string',
            'relationship' => 'required|string',
            'sms_notification' => 'required|string',
            'email_notification' => 'required|string',
            'office_address' => 'required|string',
        ];
    }
}
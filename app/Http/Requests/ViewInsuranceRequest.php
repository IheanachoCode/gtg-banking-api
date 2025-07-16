<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ViewInsuranceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'userID' => 'required|string|exists:client_registrations,user_id'
        ];
    }
}

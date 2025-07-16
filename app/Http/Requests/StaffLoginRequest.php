<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffLoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'staffID' => 'required|string',
            'password' => 'required|string'
        ];
    }
}
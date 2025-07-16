<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffRequestFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'request_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'staffID' => 'required|string|exists:staff,staffID'
        ];
    }
}
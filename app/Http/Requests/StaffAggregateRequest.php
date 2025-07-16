<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffAggregateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'staffID' => 'required|string|exists:staff,staffID'
        ];
    }
}

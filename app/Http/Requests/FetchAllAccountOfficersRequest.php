<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchAllAccountOfficersRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}

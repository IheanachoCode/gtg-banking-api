<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountOfficerFeedbackRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'feedback_type' => 'required|string',
            'feature_impacted' => 'required|string',
            'feedback_comment' => 'required|string',
            'rate' => 'required|integer|between:1,5',
            'feedback_by' => 'required|string'
        ];
    }
}
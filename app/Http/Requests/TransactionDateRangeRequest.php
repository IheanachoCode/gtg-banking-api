<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionDateRangeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'account_no' => 'required|string',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'from' => $this->from ?? now()->subMonth()->format('Y-m-d'),
            'to' => $this->to ?? now()->format('Y-m-d')
        ]);
    }
}
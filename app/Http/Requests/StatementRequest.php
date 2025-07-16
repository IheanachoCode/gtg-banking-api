<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'userID' => 'required|string',
            'account_no' => 'required|string',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from'
        ];
    }

    protected function prepareForValidation()
    {
        // Convert empty strings to null
        $this->merge([
            'from' => $this->from ?: now()->subMonths(1)->format('Y-m-d'),
            'to' => $this->to ?: now()->format('Y-m-d')
        ]);
    }
}
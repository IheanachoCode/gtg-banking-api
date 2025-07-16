<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DailyTransactionsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'sometimes|date|before_or_equal:today'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'date' => $this->date ?? now()->format('Y-m-d')
        ]);
    }
}

<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffDailyWithdrawalRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'staffID' => 'required|string|exists:staff,staffID',
            'transDate' => 'sometimes|date|before_or_equal:today'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'transDate' => $this->transDate ?? now()->format('Y-m-d')
        ]);
    }
}

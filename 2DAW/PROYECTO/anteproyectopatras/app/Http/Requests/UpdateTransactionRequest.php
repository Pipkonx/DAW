<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'category_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'nullable|numeric|min:0',
            'price_per_unit' => 'nullable|numeric|min:0',
            'currency_code' => 'nullable|string|size:3',
            'time' => 'nullable|date_format:H:i',
            'fees' => 'nullable|numeric|min:0',
            'exchange_fees' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
        ];
    }
}

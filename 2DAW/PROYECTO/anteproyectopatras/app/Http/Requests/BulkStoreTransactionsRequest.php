<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkStoreTransactionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transactions' => 'required|array|min:1',
            'transactions.*.type' => 'required|string|in:income,expense,buy,sell,dividend,reward,gift',
            'transactions.*.amount' => 'required|numeric|min:0',
            'transactions.*.date' => 'required|date',
            'transactions.*.category_id' => 'nullable|exists:categories,id',
            'transactions.*.category_name' => 'nullable|string|max:255',
            'transactions.*.description' => 'nullable|string',
            'transactions.*.asset_name' => 'nullable|string',
            'transactions.*.asset_type' => 'nullable|string|in:stock,fund,etf,crypto,bond',
            'transactions.*.portfolio_id' => 'nullable|exists:portfolios,id',
            'transactions.*.portfolio_name' => 'nullable|string|max:255',
            'transactions.*.quantity' => 'nullable|numeric|min:0',
            'transactions.*.price_per_unit' => 'nullable|numeric|min:0',
        ];
    }
}

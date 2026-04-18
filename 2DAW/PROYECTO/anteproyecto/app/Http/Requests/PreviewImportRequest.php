<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreviewImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt,pdf,jpg,jpeg,png|max:10240',
        ];
    }
}

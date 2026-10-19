<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'invoice_number' => ['required', 'string', 'max:50'],
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['required', 'email'],
            'client_address' => ['required', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'items.*.description' => 'item description',
            'items.*.quantity' => 'item quantity',
            'items.*.unit_price' => 'item unit price',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSimRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'carrier_id' => ['required','exists:carriers,id'],
            'msisdn' => ['nullable','string','max:32','unique:sims,msisdn'],
            'sim_serial' => ['nullable','string','max:32'],
            'plan_expiry_at' => ['nullable','date'],
            'is_recharged' => ['required','boolean'],
            'is_active' => ['required','boolean'],
        ];
    }
}

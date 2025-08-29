<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceRequest extends FormRequest
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
        $id = $this->route('device')->id ?? null;
        return [
            'imei' => ['required','string','max:20','regex:/^[0-9]{14,17}$/',"unique:devices,imei,{$id}"],
            'device_model_id' => ['required','exists:device_models,id'],
            'firmware' => ['nullable','string','max:64'],
            'is_active' => ['required','boolean'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentRequest extends FormRequest
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
        $id = $this->route('assignment')->id ?? null;
        return [
            'device_id'    => ['required','exists:devices,id'],
            'sim_id'       => ['nullable','exists:sims,id'],
            'vehicle_id'   => ['nullable','exists:vehicles,id'],
            'sensor_id'    => ['nullable','exists:sensors,id'],
            'is_installed' => ['required','boolean'],
            'installed_on' => ['nullable','date'],
            'removed_on'   => ['nullable','date','after_or_equal:installed_on'],
            'install_note' => ['nullable','string','max:255'],
            'is_active'    => ['required','boolean'],
        ];
    }
}

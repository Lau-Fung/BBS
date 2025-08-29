<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
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
        $id = $this->route('vehicle')->id ?? null;
        return [
            'plate' => ['required','string','max:32',"unique:vehicles,plate,{$id}"],
            'tank_capacity_liters' => ['nullable','integer','min:0','max:5000'],
            'status' => ['required','in:جاهز,صالح,خارج الخدمة,معلق'],
            'crm_no' => ['nullable','string','max:64'],
            'notes' => ['nullable','string','max:1000'],
            'supervisor_user_id' => ['nullable','exists:users,id'],
        ];
    }
}

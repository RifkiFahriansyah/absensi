<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'office_lat' => 'required|numeric|between:-90,90',
            'office_long' => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:10|max:1000',
            'work_start' => 'required|date_format:H:i',
            'late_tolerance_minutes' => 'required|integer|min:0|max:120',
        ];
    }

    public function messages(): array
    {
        return [
            'office_lat.required' => 'Latitude kantor diperlukan.',
            'office_long.required' => 'Longitude kantor diperlukan.',
            'radius_meter.required' => 'Radius diperlukan.',
            'radius_meter.max' => 'Radius maksimal hanya 1 km (1000 meter).',
            'work_start.required' => 'Jam masuk diperlukan.',
            'late_tolerance_minutes.required' => 'Toleransi keterlambatan diperlukan.',
        ];
    }
}

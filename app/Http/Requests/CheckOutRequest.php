<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo' => 'required|string', // base64 encoded image
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required' => 'Lokasi GPS diperlukan.',
            'longitude.required' => 'Lokasi GPS diperlukan.',
            'photo.required' => 'Foto selfie diperlukan.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SensorDataReceiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Hardcode daftar type yang diizinkan
        $allowedTypes = ['epever_data', 'sensor_rs485', 'sensor_arduino'];

        return [
            'mac_address' => 'required|string',
            'sensors' => 'required|array',
            'type' => ['required', 'string', Rule::in($allowedTypes)],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}

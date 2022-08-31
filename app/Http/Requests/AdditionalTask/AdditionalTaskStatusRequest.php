<?php

namespace App\Http\Requests\AdditionalTask;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AdditionalTaskStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'additional_task_status' => ['required', 'string'],
            'notes' => ['required_if:additional_task_status,Decline,Pospone,Canceled', 'string'], // by reciever
            'pospone_until' => ['required_if:additional_task_status,Pospone'] // by reciever
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'icon' => 'error',
            'title' => 'Validation Error',
            'message' => $validator->errors()->first(),
        ], 422));
    }
}

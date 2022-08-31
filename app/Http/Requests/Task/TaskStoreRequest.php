<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class TaskStoreRequest extends FormRequest
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
            'group' => ['nullable', 'string', 'max:20'],
            'title' => ['required', 'string', 'max:30', 'unique:tasks,title,except,id'],
            'description' => ['required', 'string'],
            'due_date' => ['required', 'string'],
            'set_as_priority' => ['nullable', 'string'],
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

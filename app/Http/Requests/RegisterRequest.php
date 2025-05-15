<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            //'name' => 'required|string|unique:users,name'
        ];
    }

    /**
     * @throws ApiException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        if ($errors->count()) {
            $messages = array_values($errors->messages());
            $message = $messages[0][0];
            throw new ApiException($message, 422, ApiException::VALIDATION_ERROR);
        }
    }
}

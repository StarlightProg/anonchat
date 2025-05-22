<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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

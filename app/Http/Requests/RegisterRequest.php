<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            //'name' => 'required|string|unique:users,name'
        ];
    }
}

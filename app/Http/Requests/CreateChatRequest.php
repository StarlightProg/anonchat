<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateChatRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'socket_first_id' => ['required', 'string'],
            'socket_second_id' => ['required', 'string']
        ];
    }
}

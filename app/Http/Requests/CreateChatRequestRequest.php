<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateChatRequestRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'socket_first_id' => ['required', 'string'],
            'socket_second_id' => ['required', 'string']
        ];
    }
}

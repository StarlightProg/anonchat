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
            'group_id' => ['required', 'string']
        ];
    }
}

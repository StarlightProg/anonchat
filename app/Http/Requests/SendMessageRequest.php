<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendMessageRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'group_id' => ['required', 'string', Rule::exists('chats', 'id')],
            'message' => ['string'],
        ];
    }
}

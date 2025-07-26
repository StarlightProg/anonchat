<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'group_id' => $this->group_id,
            'client' => new UserResource($this->client()),
            'message' => $this->message,
            'time' => Carbon::parse($this->created_at)->format('H:i'),
            'is_you' => ($request->user()->id === $this->client()->id) ? true : false
        ];
    }
}

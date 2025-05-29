<?php

namespace App\Http\Resources;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    public function toArray($request)
    {
        $chat_messages = ChatMessage::where('group_id', $this->id);
        return [
            'group_id' => $this->id,
            'last_message' => new ChatMessageResource($chat_messages->orderByDesc('id')->first()),
            'unread_messages' => $chat_messages->where('client_id', '!=', $request->user()->id)->where('is_read', false)->count(),
        ];
    }
}

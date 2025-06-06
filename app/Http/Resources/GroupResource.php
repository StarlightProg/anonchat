<?php

namespace App\Http\Resources;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class GroupResource extends JsonResource
{
    public function toArray($request)
    {
        Log::info("this " . json_encode($this));
        $chat_messages = ChatMessage::where('group_id', $this->group_id);
        
        return [
            'group_id' => $this->group_id,
            'chat_name' => $this->client()->name,
            'last_message' => new ChatMessageResource($chat_messages->orderByDesc('id')->first()),
            'unread_messages' => $chat_messages->where('client_id', '!=', $request->user()->id)->where('is_read', false)->count(),
        ];
    }
}

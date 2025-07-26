<?php

namespace App\Http\Resources;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupResource extends JsonResource
{
    public function toArray($request)
    {
        Log::info("this " . json_encode($this));
        $chat_messages = ChatMessage::where('group_id', $this->group_id);
        $chat_name = DB::table('chats')->where('chats.id', $this->group_id)
            ->join('clients_in_chats', 'clients_in_chats.group_id', '=', 'chats.id')
            ->join('users', 'clients_in_chats.client_id', '=', 'users.id')
            ->where('clients_in_chats.client_id', '!=', $this->client()->id)
            ->pluck('users.name')->first();
        
        return [
            'group_id' => $this->group_id,
            'chat_name' => $chat_name,
            'last_message' => new ChatMessageResource($chat_messages->orderByDesc('id')->first()),
            'unread_messages' => $chat_messages->where('client_id', '!=', $request->user()->id)->where('is_read', false)->count(),
        ];
    }
}

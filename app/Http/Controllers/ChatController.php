<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Resources\Collections\GroupCollection;
use App\Http\Requests\CreateChatRequest;
use App\Http\Requests\CreateChatRequestRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\Collections\ChatMessagesCollection;
use App\Http\Resources\GroupResource;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\ChatRequest;
use App\Models\ClientsInChat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ChatController extends ApiController
{
    public function create_chat_request(CreateChatRequestRequest $request){
        $chat_request = ChatRequest::updateOrCreate(
            ["client_id" => $request->user()->id], 
            [
                "socket_first_id" => $request->socket_first_id,
                "socket_second_id" => $request->socket_second_id,
                "name" => $request->name,
                "age" => $request->age,
            ]
        );

        return $this->success(["request_id" => $chat_request->id]);
    }

    public function create_chat(CreateChatRequest $request){
        $chat_request = ChatRequest::where('id', $request->request_id)
            ->where('socket_first_id', $request->socket_first_id)
            ->where('socket_second_id', $request->socket_second_id);

        if(!$chat_request->exists()){
            $this->failure("Нет такого чата", 404, ApiException::CHAT_NOT_FOUND);
        }

        $chat_request = $chat_request->first();

        $chat = Chat::create([
            "id" => Str::random(50)
        ]);

        Log::info("Chat ID: " . $chat->id);

        $clients_in_chats = [
            [
                'group_id' => $chat->id,
                'client_id' => $request->user()->id,
                'name' => $chat_request->name,
            ],
            [
                'group_id' => $chat->id,
                'client_id' => $chat_request->client_id,
                'name' => $request->name,
            ]
        ];

        foreach ($clients_in_chats as $data) {
            ClientsInChat::create($data);
        }
        
        return $this->success(['chat_id' => $chat->id]);
    }

    public function send_message(CreateChatRequest $request){
        $message = ChatMessage::create([
            "group_id" => $request->group_id,
            "client_id" => $request->user()->id,
            "message" => $request->message
        ]);
        
        return $this->success(['message' => new ChatMessageResource($message)]);
    }

    public function chat_list(Request $request){
        $groups = $request->user()->chatGroups()->paginate($request->limit, ['*'], 'start', $request->page);

        return $this->success(new GroupCollection($groups));
    }

    public function chat_data(Request $request, $group_id){
        $messages = $request->chat->lastMessages()->paginate($request->limit, ['*'], 'start', $request->page);
        
        return $this->success(new ChatMessagesCollection($messages));
    }
}

<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\CreateChatRequest;
use App\Http\Requests\CreateChatRequestRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Chat;
use App\Models\ChatRequest;
use App\Models\ClientsInChat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChatController extends ApiController
{
    public function create_chat_request(CreateChatRequestRequest $request){
        ChatRequest::updateOrCreate(
            ["client_id" => $request->user()->id], 
            [
                "socket_first_id" => $request->socket_first_id,
                "socket_second_id" => $request->socket_second_id,
                "name" => $request->name,
                "age" => $request->age,
            ]
        );

        return $this->success();
    }

    public function create_chat(CreateChatRequest $request){
        $chat_request = ChatRequest::where('client_id', $request->client_id)
            ->where('socket_first_id', $request->socket_first_id)
            ->where('socket_second_id', $request->socket_second_id);

        if(!$chat_request->exists()){
            $this->failure("Нет такого чата", 404, ApiException::CHAT_NOT_FOUND);
        }

        $chat = Chat::firstOrCreate([
            "id" => Str::uuid()
        ]);

        $clients_in_chats = [
            [
                'group_id' => $chat->id,
                'client_id' => $request->user()->id,
                'name' => $chat_request->name,
                'age' => $chat_request->age,
            ],
            [
                'group_id' => $chat->id,
                'client_id' => $request->client_id,
                'name' => $request->name,
                'age' => $request->age,
            ]
        ];

        ClientsInChat::firstOrCreate($clients_in_chats);
        
        return $this->success();
    }
}

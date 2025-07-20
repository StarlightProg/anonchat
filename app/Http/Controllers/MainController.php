<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(){
        return view('main');
    }

    public function chats(){
        return view('chats');
    }

    public function chats_with_id($chat_id){
        return view('chats', ['chat_id' => $chat_id]);
    }
}

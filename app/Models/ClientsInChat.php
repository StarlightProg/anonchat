<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientsInChat extends Model
{
    use HasFactory;

    public $fillable = [
        'id',
        'group_id',
        'client_id',
        'name',
        'age',
    ];

    public function client(){
        return User::where('id', $this->client_id)->first();
    }

    public function lastMessages(){
        return ChatMessage::where('group_id', $this->group_id)->orderByDesc('created_at');
    }
}

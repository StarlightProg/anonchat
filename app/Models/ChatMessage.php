<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'client_id',
        'message'
    ];

    public function client(){
        return User::where('id', $this->client_id)->first();
    }
}

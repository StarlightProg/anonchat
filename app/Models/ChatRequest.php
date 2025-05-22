<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRequest extends Model
{
    use HasFactory;

    public $fillable = [
        'client_id',
        'socket_first_id',
        'socket_second_id',
        'name',
        'age',
    ];
}

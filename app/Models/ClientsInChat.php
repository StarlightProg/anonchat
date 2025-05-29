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
}

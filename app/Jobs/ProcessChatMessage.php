<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessChatMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chat;

    public function __construct($chat)
    {
        $this->chat = $chat;
    }

    public function handle()
    {
        // Логирование полученного сообщения
        $data = $this->chat;
        Log::info(json_encode($this->chat));
        //Log::info("Message received from user {$data['user']}: {$data['text']}");
    }
}

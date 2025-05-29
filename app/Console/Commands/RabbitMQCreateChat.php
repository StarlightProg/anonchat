<?php

namespace App\Console\Commands;

use App\Jobs\CreateChat;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Jobs\HandlePersistentChatRequest;

class RabbitMQCreateChat extends Command
{
    protected $signature = 'rabbitmq:consume-persistent-chat';
    protected $description = 'Consume persistent chat requests from RabbitMQ';

    public function handle()
    {
        $connection = new AMQPStreamConnection(
            config('services.rabbit_mq.host'),
            config('services.rabbit_mq.port'),
            config('services.rabbit_mq.user'),
            config('services.rabbit_mq.password')
        );
        $channel = $connection->channel();
        $channel->queue_declare('create-chat', false, true, false, false);

        $callback = function (AMQPMessage $msg) {
            $payload = json_decode($msg->getBody(), true);
            if ($payload) {
                dispatch(new CreateChat($payload));
                echo "Job dispatched\n";
            }
            $msg->ack();
        };

        $channel->basic_qos(null, 1, null);
        $channel->basic_consume('persistent_chat_requests', '', false, false, false, false, $callback);

        echo "Waiting RabbitMQ messages...\n";
        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}


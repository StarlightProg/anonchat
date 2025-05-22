<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQConnection
{
    private static $connection = null;
    private static $channel = null;

    public static function getChannel()
    {
        if (self::$channel === null) {
            self::$connection = new AMQPStreamConnection(
                config('services.rabbit_mq.host'),
                config('services.rabbit_mq.port'),
                config('services.rabbit_mq.user'),
                config('services.rabbit_mq.password')
            );
            self::$channel = self::$connection->channel();
            self::$channel->queue_declare('chat_responses', false, true, false, false);
        }

        return self::$channel;
    }

    public static function close()
    {
        if (self::$channel !== null) {
            self::$channel->close();
            self::$connection->close();
            self::$channel = null;
            self::$connection = null;
        }
    }
}

<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

const QUEUE_NAME = 'q.1_simple';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare(QUEUE_NAME);

$callback = static function (AMQPMessage $msg) {
    echo ' [x] Received ', $msg->body, "\n";
};

$channel->basic_consume(QUEUE_NAME, no_ack: true, callback: $callback);

while ($channel->is_open()) {
    $channel->wait();
}

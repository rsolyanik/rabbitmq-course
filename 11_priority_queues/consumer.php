<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

const QUEUE_NAME = 'ex.11_priority_queue';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');

$channel = $connection->channel();
$channel->queue_declare(QUEUE_NAME, arguments: new AMQPTable(['x-max-priority' => 10]));

$callback = static function (AMQPMessage $msg) {
    echo ' [x] Received ', $msg->body, "\n";
};

$channel->basic_consume(QUEUE_NAME, no_ack: true, callback: $callback);

while ($channel->is_open()) {
    $channel->wait();
}

<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

const QUEUE_NAME = 'q.10_lazy';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();


$channel->queue_declare(QUEUE_NAME, durable: true);

for ($i = 0; $i < 1000000; $i++) {
    $text = 'message' . $i;
    $channel->basic_publish(new AMQPMessage($text, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]), '', QUEUE_NAME);

}
echo " [x] Sent $i messages\n";

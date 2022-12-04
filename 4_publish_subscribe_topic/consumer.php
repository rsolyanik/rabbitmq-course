<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

const EXCHANGE_NAME = 'ex.4_topic';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare(EXCHANGE_NAME, AMQPExchangeType::TOPIC);
$queueName = $channel->queue_declare()[0];

$routingKeys = array_slice($argv, 1);

$channel->queue_bind($queueName, EXCHANGE_NAME, '*.logs.error');
$channel->queue_bind($queueName, EXCHANGE_NAME, 'aplication1.#');


$callback = static function (AMQPMessage $msg) {
    echo ' [x] Received ', $msg->body, "\n";
};

$channel->basic_consume($queueName, no_ack: true, callback: $callback);

while ($channel->is_open()) {
    $channel->wait();
}

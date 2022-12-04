<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

const EXCHANGE_NAME = 'ex.5_headers';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare(EXCHANGE_NAME, AMQPExchangeType::HEADERS);
$queueName = $channel->queue_declare()[0];

$channel->queue_bind(
    $queueName,
    EXCHANGE_NAME,
    arguments: new AMQPTable([
        'x-match' => 'all',
        'type' => 'not logs',
        'level' => 'info'
    ]));


$callback = static function (AMQPMessage $msg) {
    echo ' [x] Received ', $msg->body, "\n";
};

$channel->basic_consume($queueName, no_ack: true, callback: $callback);

while ($channel->is_open()) {
    $channel->wait();
}

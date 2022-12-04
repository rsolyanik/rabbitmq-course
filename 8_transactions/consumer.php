<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

const EXCHANGE_NAME = 'ex.8_transactions';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare(EXCHANGE_NAME, AMQPExchangeType::DIRECT);
$queueName = $channel->queue_declare()[0];

$channel->queue_bind($queueName, EXCHANGE_NAME, '');

$callback = static function (AMQPMessage $msg) use ($channel) {
    echo ' [x] Received ', $msg->body, "\n";
    $channel->basic_ack($msg->getDeliveryTag());
    $channel->tx_rollback();
};

$channel->tx_select();

$channel->basic_consume($queueName, no_ack: false, callback: $callback);

while ($channel->is_open()) {
    $channel->wait();
}



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


echo " [x] Sent \n";


$channel->basic_publish(
    new AMQPMessage('logs error', ['application_headers' => new AMQPTable(['type' => 'logs', 'level' => 'error'])]),
    EXCHANGE_NAME
);
$channel->basic_publish(
    new AMQPMessage('logs warning', ['application_headers' => new AMQPTable(['type' => 'logs', 'level' => 'warning'])]),
    EXCHANGE_NAME
);
$channel->basic_publish(
    new AMQPMessage('logs info', ['application_headers' => new AMQPTable(['type' => 'logs', 'level' => 'info'])]),
    EXCHANGE_NAME
);
$channel->basic_publish(
    new AMQPMessage('not logs info', ['application_headers' => new AMQPTable(['type' => 'not logs', 'level' => 'info'])]),
    EXCHANGE_NAME
);
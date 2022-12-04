<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

const EXCHANGE_NAME = 'ex.3_direct';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->exchange_declare(EXCHANGE_NAME, AMQPExchangeType::DIRECT);

$msgText = $argv[1];

echo " [x] Sent '$msgText'\n";


$channel->basic_publish(new AMQPMessage($msgText), EXCHANGE_NAME, 'error');
$channel->basic_publish(new AMQPMessage($msgText), EXCHANGE_NAME, 'warning');
$channel->basic_publish(new AMQPMessage($msgText), EXCHANGE_NAME, 'info');

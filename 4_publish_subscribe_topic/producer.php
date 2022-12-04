<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

const EXCHANGE_NAME = 'ex.4_topic';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->exchange_declare(EXCHANGE_NAME, AMQPExchangeType::TOPIC);

$msgText = $argv[1];

echo " [x] Sent '$msgText'\n";


$channel->basic_publish(new AMQPMessage($msgText), EXCHANGE_NAME, 'aplication1.logs.error');
$channel->basic_publish(new AMQPMessage($msgText), EXCHANGE_NAME, 'aplication1.logs.info');
$channel->basic_publish(new AMQPMessage($msgText), EXCHANGE_NAME, 'aplication1.metrics.memory');
$channel->basic_publish(new AMQPMessage($msgText), EXCHANGE_NAME, 'aplication1.metrics.cpu');
$channel->basic_publish(new AMQPMessage($msgText), EXCHANGE_NAME, 'aplication2.logs.error');

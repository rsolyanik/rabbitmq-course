<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

const EXCHANGE_NAME = 'ex.8_transactions';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->exchange_declare(EXCHANGE_NAME, AMQPExchangeType::DIRECT);

$msgText = '123';

echo " [x] Sent '\n";

$channel->tx_select();
$channel->basic_publish(new AMQPMessage($msgText), EXCHANGE_NAME);
$channel->basic_publish(new AMQPMessage($msgText), EXCHANGE_NAME);
$channel->basic_publish(new AMQPMessage($msgText), EXCHANGE_NAME);
$channel->tx_commit();

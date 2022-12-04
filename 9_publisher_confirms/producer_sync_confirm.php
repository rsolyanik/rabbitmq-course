<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

const EXCHANGE_NAME = 'ex.9_publisher_confirm';
const QUEUE_NAME = 'q.9_publisher_confirm';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->exchange_declare(EXCHANGE_NAME, 'direct');
$channel->queue_declare(QUEUE_NAME);
$channel->queue_bind(QUEUE_NAME, EXCHANGE_NAME);
$msgText = '123';

echo " [x] Sent '\n";

$channel->confirm_select();
$channel->basic_publish(new AMQPMessage($msgText), EXCHANGE_NAME);
$channel->wait_for_pending_acks(5.000);



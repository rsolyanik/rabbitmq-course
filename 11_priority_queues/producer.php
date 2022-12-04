<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

const QUEUE_NAME = 'ex.11_priority_queue';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare(QUEUE_NAME, arguments: new AMQPTable(['x-max-priority' => 10]));


echo " [x] Sent '\n";

$channel->basic_publish(new AMQPMessage('3', ['priority' => 7]), '', QUEUE_NAME);
$channel->basic_publish(new AMQPMessage('1', ['priority' => 10]), '', QUEUE_NAME);
$channel->basic_publish(new AMQPMessage('2', ['priority' => 8]), '', QUEUE_NAME);
$channel->basic_publish(new AMQPMessage('4', ['priority' => 6]), '', QUEUE_NAME);

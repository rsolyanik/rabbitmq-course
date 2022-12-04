<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

const QUEUE_NAME = 'q.1_simple';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare(QUEUE_NAME);

$msgText = $argv[1];

echo " [x] Sent '$msgText'\n";


$channel->basic_publish(new AMQPMessage($msgText), '', QUEUE_NAME);

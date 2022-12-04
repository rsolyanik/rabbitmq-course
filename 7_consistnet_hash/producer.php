<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

const EXCHANGE_NAME = 'ex.7_consistent_hash';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->exchange_declare(EXCHANGE_NAME, 'x-consistent-hash');

$queue1Name = 'queue 1';
$queue2Name = 'queue 2';

$channel->queue_declare($queue1Name);
$channel->queue_declare($queue2Name);

$channel->queue_purge($queue1Name);
$channel->queue_purge($queue2Name);


for ($i = 0; $i < 1000; $i++) {
    $text = 'message' . $i;
    $channel->basic_publish(new AMQPMessage($text), EXCHANGE_NAME, $i);

}
echo " [x] Sent $i messages\n";

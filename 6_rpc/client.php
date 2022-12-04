<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

const QUEUE_NAME = 'q.rpc';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();


echo " [x] Sent \n";
$replyQueueName = $channel->queue_declare(exclusive: true)[0];

$correlationId = uniqid('cID', true);

$message = new AMQPMessage($argv[1], [
    'correlation_id' => $correlationId,
    'reply_to' => $replyQueueName,
]);

$channel->basic_publish(
    $message,
    '',
    QUEUE_NAME,
);

$response = null;
$channel->basic_consume($replyQueueName, no_ack: true, callback: static function (AMQPMessage $msg) use (
    $correlationId,
    &$response
) {
    if ($msg->get_properties()['correlation_id'] === $correlationId) {
        $response = $msg;
        echo " [x] Response: '$msg->body'\n";
    }
});

while ($response === null) {
    $channel->wait();
}

<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

const QUEUE_NAME = 'q.rpc';

$connection = new AMQPStreamConnection('my_rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->basic_qos(0, 1, true);

$channel->queue_declare(QUEUE_NAME);
$channel->queue_purge(QUEUE_NAME);


$callback = static function (AMQPMessage $msg) use ($channel) {
    $properties = $msg->get_properties();
    $replyTo = $properties['reply_to'];
    $correlationId = $properties['correlation_id'];


    echo ' [x] Received ', $msg->body, "\n";

    $replyMsgText = "reply to $msg->body";
    $replyMsq = new AMQPMessage($replyMsgText, [
        'correlation_id' => $correlationId,
    ]);

    echo " [x] Reply $replyMsgText, correlation id $correlationId, to $replyTo";

    $channel->basic_publish($replyMsq, '', $replyTo);
    $channel->basic_ack($msg->getDeliveryTag());
};

$channel->basic_consume(QUEUE_NAME, callback: $callback);

while ($channel->is_open()) {
    $channel->wait();
}

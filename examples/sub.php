<?php
$cnn = new AMQPConnection([
    'host' => 'localhost',
    'port' => 5672,
    'user' => 'guest',
    'password' => 'guest',
    'vhost' => '/'
]);
$cnn->connect();

// Create a channel
$ch = new AMQPChannel($cnn);

// Declare a new exchange
$ex = new AMQPExchange($ch);
$ex->setName('default');
$ex->setType("direct");
$ex->declareExchange();

// Create a new queue
$q = new AMQPQueue($ch);
$q->setName('queue1');
$q->declareQueue();
// Bind it on the exchange to routing.key
$q->bind('default', 'routing.key');

$q->consume('consume', AMQP_AUTOACK);

function consume($message) {
    echo $message->getBody();
}

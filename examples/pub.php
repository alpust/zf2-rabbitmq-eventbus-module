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


$ex->publish('hello amqp!');
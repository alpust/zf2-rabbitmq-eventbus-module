<?php


namespace AMQPModule\Service;


class BusConsumer
{

    protected $queue;

    protected $exchange;

    protected $callback;

    public function __construct(
        \AMQPQueue $queue,
        $exchange
    ){
        $this->queue = $queue;
        $this->exchange = $exchange;
    }


    public function consume($event, callable $callback)
    {

        $this->callback = $callback;

        if(!$this->queue->bind($this->exchange, $event)) {
            throw new \Exception('Can not bind ' . $this->queue->getName() . ' to an exchange ' . $this->exchange);
        }
        $this->queue->consume([$this, 'processMessage'], AMQP_AUTOACK);
    }

    protected function processMessage(\AMQPEnvelope $message)
    {
        call_user_func($this->callback, $this->unpackMessage($message));
    }

    protected function unpackMessage(\AMQPEnvelope $message)
    {
        if($message->getContentType() == 'text/plain') {
            return $message->getBody();
        } elseif ($message->getContentType() == 'serialized/array') {
            return unserialize($message->getBody());
        }

        throw new \Exception('Unsupported message content type ' . $message->getContentType());
    }

}
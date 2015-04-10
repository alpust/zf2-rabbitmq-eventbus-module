<?php
namespace AMQPModule;

use Zend\Stdlib\ArrayUtils;

class Consumer
{

    /** @var \AMQPQueue  */
    protected $queue;

    protected $defaultConfig;

    function __construct(
        \AMQPQueue $queue,
        $config = [
            'flags' => AMQP_AUTOACK,
            'tag' => null
        ]
    ){
        $this->queue = $queue;
        $this->defaultConfig = $config;
    }

    public function consume(callable $callback, $flags = AMQP_AUTOACK, $tag = null)
    {
        $this->queue->consume($callback, AMQP_AUTOACK);
    }

}
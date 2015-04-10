<?php
namespace AMQPModule\Service;

use Zend\Stdlib\ArrayUtils;

class Consumer
{

    /** @var \AMQPQueue  */
    protected $queue;

    protected $defaultConfig = [
        'flags' => AMQP_AUTOACK,
        'tag' => null
    ];

    function __construct(
        \AMQPQueue $queue,
        $config = []
    ){
        $this->queue = $queue;
        $this->defaultConfig = array_merge($this->defaultConfig, $config);
    }

    public function consume(callable $callback, $flags = AMQP_AUTOACK, $tag = null)
    {
        $this->queue->consume($callback, AMQP_AUTOACK);
    }

}
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
        try {
            $config = $this->defaultConfig;
            if (func_num_args() > 1) {
                $config = $this->getQueueConfig($flags, $tag);
            }

            $this->queue->consume($callback, $config['flags'], $config['tag']);
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getQueueConfig($flags, $tag)
    {
        $config = $this->defaultConfig;

        if($flags !== AMQP_AUTOACK) {
            $config['flags'] = $flags;
        }

        if(!empty($tag)) {
            $config['tag'] = $tag;
        }

        return $config;
    }

}
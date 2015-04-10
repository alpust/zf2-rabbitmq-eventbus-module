<?php
namespace AMQPModule\Service;


use Zend\Stdlib\ArrayUtils;

class Publisher
{

    /** @var \AMQPExchange  */
    protected $exchange;

    /** @var array  */
    protected $defaultConfig;

    function __construct(
        \AMQPExchange $exchange,
        $config = [
            'routing_key' => null,
            'flags' => AMQP_NOPARAM,
            'attributes' => []
        ]
    ){
        $this->exchange = $exchange;
        $this->defaultConfig = $config;
    }

    public function publish($message, $routingKey = null, $flags = AMQP_NOPARAM, array $attributes = [])
    {

        try {
            $config = $this->defaultConfig;
            if(func_num_args() > 1) {
                $config = $this->getMessageConfig($routingKey, $flags, $attributes);
            }

            return $this->exchange->publish($message, $config['routing_key'], $config['flags'], $config['arguments']);
        } catch(\Exception $e) {
            return false;
        }
    }

    protected function getMessageConfig($routingKey = null, $flags = AMQP_NOPARAM, array $attributes = [])
    {
        $config = $this->defaultConfig;
        if($routingKey) {
            $config['routing_key'] = $routingKey;
        }

        if($flags !== AMQP_NOPARAM) {
            $config['flags'] = $flags;
        }

        if(!empty($attributes) && is_array($attributes)) {
            $config['attributes'] = $attributes;
        }

        return $config;
    }

}
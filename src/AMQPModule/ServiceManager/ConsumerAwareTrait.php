<?php
namespace AMQPModule;

use AMQPModule\Service\Consumer;

class ConsumerAwareTrait
{

    protected $consumer;

    protected $consumerName = 'default';

    /**
     * @return string
     */
    public function getConsumerName()
    {
        return $this->consumerName;
    }

    /**
     * @param Consumer $consumer
     */
    public function setConsumer(Consumer $consumer)
    {
        $this->consumer = $consumer;
    }

    /**
     * @return mixed
     */
    public function getConsumer()
    {
        return $this->consumer;
    }

}
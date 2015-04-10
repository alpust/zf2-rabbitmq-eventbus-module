<?php
namespace AMQPModule;


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
     * @param Publisher $consumer
     * @return mixed
     */
    public function setConsumer(Publisher $consumer)
    {
        $this->consumer = $consumer;
    }

    /**
     * @return Publisher
     */
    public function getConsumer()
    {
        return $this->consumer;
    }

}
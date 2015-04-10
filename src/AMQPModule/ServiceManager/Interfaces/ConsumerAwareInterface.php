<?php
namespace AMQPModule\ServiceManager\Interfaces;

use AMQPModule\Service\Consumer;

interface ConsumerAwareInterface
{

    /**
     * @return string
     */
    public function getConsumerName();

    /**
     * @param Consumer $consumer
     * @return mixed
     */
    public function setConsumer(Consumer $consumer);

    /**
     * @return Consumer
     */
    public function getConsumer();

}
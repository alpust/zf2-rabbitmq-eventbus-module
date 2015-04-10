<?php


namespace AMQPModule\ServiceManager\Interfaces;


use AMQPModule\Service\BusConsumer;

interface BusConsumerAwareInterface
{

    /**
     * @return BusConsumer
     */
    public function getBusConsumer();

    /**
     * @param BusConsumer $consumer
     * @return mixed
     */
    public function setBusConsumer(BusConsumer $consumer);

}
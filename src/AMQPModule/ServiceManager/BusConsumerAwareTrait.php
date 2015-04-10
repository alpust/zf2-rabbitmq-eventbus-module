<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 4/10/15
 * Time: 11:25 PM
 */

namespace AMQPModule\ServiceManager;


use AMQPModule\Service\BusConsumer;

trait BusConsumerAwareTrait
{

    /**
     * @var BusConsumer
     */
    protected $messageBusConsumer;

    /**
     * @return BusConsumer
     */
    public function getBusConsumer()
    {
        return $this->messageBusConsumer;
    }

    /**
     * @param BusConsumer $consumer
     * @return mixed
     */
    public function setBusConsumer(BusConsumer $consumer)
    {
        $this->messageBusConsumer = $consumer;
    }

}
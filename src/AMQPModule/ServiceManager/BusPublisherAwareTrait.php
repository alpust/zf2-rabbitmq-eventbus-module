<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 4/10/15
 * Time: 11:25 PM
 */

namespace AMQPModule\ServiceManager;


use AMQPModule\Service\BusPublisher;

trait BusPublisherAwareTrait
{

    /**
     * @var BusPublisher
     */
    protected $messageBusPublisher;

    /**
     * @return BusPublisher
     */
    public function getBusPublisher()
    {
        return $this->messageBusPublisher;
    }

    /**
     * @param BusPublisher $consumer
     * @return mixed
     */
    public function setBusPublisher(BusPublisher $publisher)
    {
        $this->messageBusPublisher = $publisher;
    }

}
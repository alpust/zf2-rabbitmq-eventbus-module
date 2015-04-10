<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 4/10/15
 * Time: 11:20 PM
 */

namespace AMQPModule\ServiceManager\Interfaces;


use AMQPModule\ServiceManager\Factory\BusPublisher;

interface BusPublisherAwareInterface
{

    /**
     * @return BusPublisher
     */
    public function getBusPublisher();

    /**
     * @param BusPublisher $publisher
     * @return mixed
     */
    public function setBusPublisher(BusPublisher $publisher);

}
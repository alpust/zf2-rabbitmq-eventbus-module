<?php
namespace AMQPModule\ServiceManager\Interfaces;

use AMQPModule\Service\Publisher;

interface PublisherAwareInterface
{

    /**
     * @return string
     */
    public function getPublisherName();

    /**
     * @param Publisher $publisher
     * @return mixed
     */
    public function setPublisher(Publisher $publisher);

    /**
     * @return Publisher
     */
    public function getPublisher();

}
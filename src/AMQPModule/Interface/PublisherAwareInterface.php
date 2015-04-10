<?php
namespace AMQPModule\Interfaces;


use AMQPModule\Publisher;

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
<?php
namespace AMQPModule;


trait PublisherAwareTrait
{

    protected $publisher;

    protected $publisherName  = 'default';

    /**
     * @return string
     */
    public function getPublisherName()
    {
        return $this->publisherName;
    }

    /**
     * @param Publisher $publisher
     * @return mixed
     */
    public function setPublisher(Publisher $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * @return Publisher
     */
    public function getPublisher()
    {
        return $this->publisher;
    }


}
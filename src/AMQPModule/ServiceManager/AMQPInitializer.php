<?php
namespace AMQPModule\ServiceManager;

use AMQPModule\ServiceManager\Interfaces\AMQPAwareInterface;
use AMQPModule\ServiceManager\Interfaces\ConsumerAwareInterface;
use AMQPModule\ServiceManager\Interfaces\PublisherAwareInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AMQPInitializer implements InitializerInterface
{
    /**
     * Initialize
     *
     * @param $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceof PublisherAwareInterface || $instance instanceof AMQPAwareInterface) {
            $publisher = $serviceLocator->get($instance->getPublisherName());
            $instance->setPublisher($publisher);
        }

        if ($instance instanceof ConsumerAwareInterface || $instance instanceof AMQPAwareInterface) {
            $consumer = $serviceLocator->get($instance->getConsumerName());
            $instance->setConsumer($consumer);
        }
    }


}
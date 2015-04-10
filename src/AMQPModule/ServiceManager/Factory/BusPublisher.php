<?php


namespace AMQPModule\ServiceManager\Factory;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use AMQPModule\Service\BusPublisher as ServiceBusPublisher;

class BusPublisher implements FactoryInterface
{

    protected $configKey = 'amqp';

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ServiceBusPublisher($serviceLocator->get('amqp.exchanges.messageBus'));
    }


}
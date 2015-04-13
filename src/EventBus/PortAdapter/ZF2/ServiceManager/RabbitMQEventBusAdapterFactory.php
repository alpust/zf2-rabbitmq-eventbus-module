<?php
namespace EventBus\PortAdapter\ZF2\ServiceManager;


use EventBus\PortAdapter\RabbitMQ\EventBusAdapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RabbitMQEventBusAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $exchange = $serviceLocator->get('amqp.exchanges.messageBus');
        $queue = new \AMQPQueue($exchange->getChannel());
        /** @TODO make queue application-dependable */
//        $queue->setName();
//        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();

        return new EventBusAdapter($queue, $exchange);
    }


}
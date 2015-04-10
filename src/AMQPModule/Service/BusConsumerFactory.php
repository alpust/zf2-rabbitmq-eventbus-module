<?php


namespace AMQPModule\Service;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BusConsumerFactory implements FactoryInterface
{

    protected static $queues = [];

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        /** @var \AMQPExchange $exchange */
        $exchange = $serviceLocator->get('amqp.exchanges.messageBus');

        $queue = new \AMQPQueue($exchange->getChannel());
        $queue->setName('messageBusQueue' . count($this::$queues));
        $queue->declareQueue();
        $this::$queues[$queue->getName()] = $queue;

        return new BusConsumer($queue, $exchange->getName());
    }

}
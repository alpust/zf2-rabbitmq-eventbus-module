<?php
namespace AMQPModule\ServiceManager\Factory;

use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\ServiceLocatorInterface;

class QueueAbstractFactory extends AMQPAbstractFactory
{

    protected $configSubKey = 'queues';

    protected $defaults = [
        'name' => 'default_queue',
        'flags' => AMQP_NOPARAM,
        'arguments' => []
    ];

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $requested = explode('.', $requestedName)[2];
        $queueConfig = array_merge($this->defaults, $this->getServiceConfig($serviceLocator, $requested));

        $exchangeName = $this->configKey . '.exchanges.default';
        if(!empty($queueConfig['exchange']) && is_string($queueConfig['exchange'])) {
            $exchangeName = $this->configKey . '.exchanges.' . $queueConfig['exchange'];
        }
        /** @var \AMQPExchange $exchange */
        $exchange = $serviceLocator->get($exchangeName);

        $queue = new \AMQPQueue($exchange->getChannel());
        $queue->setName($queueConfig['name']);
        $queue->setFlags($queueConfig['flags']);
        $queue->setArguments($queueConfig['arguments']);
        $queue->declareQueue();
        if(!$queue->bind($exchange->getName())) {
            throw new ServiceNotCreatedException('Can not bind ' . $queue->getName() . ' to an exchange ' . $exchange->getName());
        }

        return $queue;
    }


}
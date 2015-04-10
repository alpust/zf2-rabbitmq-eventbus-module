<?php
namespace AMQPModule\ServiceManager\Factory;

use AMQPModule\Service\Consumer;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConsumerAbstractFactory extends AMQPAbstractFactory
{

    protected $configSubKey = 'consumers';


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

        $consumerConfig = $this->getServiceConfig($serviceLocator, $requested);

        $queueName = $this->configKey . '.queues.default';
        if(!empty($consumerConfig['queue']) && is_string($consumerConfig['queue'])) {
            $queueName = $this->configKey . '.queues.' . $consumerConfig['queue'];
        }

        return new Consumer(
            $serviceLocator->get($queueName),
            $consumerConfig
        );
    }

}
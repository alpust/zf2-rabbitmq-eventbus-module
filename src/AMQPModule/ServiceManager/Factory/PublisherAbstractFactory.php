<?php
namespace AMQPModule\ServiceManager\Factory;

use AMQPModule\Service\Publisher;
use Zend\ServiceManager\ServiceLocatorInterface;

class PublisherAbstractFactory extends AMQPAbstractFactory
{

    protected $configSubKey = 'publishers';

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

        $publisherConfig = $this->getServiceConfig($serviceLocator, $requested);

        $exchangeName = $this->configKey . '.exchanges.default';
        if(isset($publisherConfig['exchange']) && is_string($publisherConfig['exchange'])) {
            $exchangeName = $this->configKey . '.exchanges.' . $publisherConfig['exchange'];
        }

        return new Publisher(
            $serviceLocator->get($exchangeName),
            $publisherConfig
        );
    }

}
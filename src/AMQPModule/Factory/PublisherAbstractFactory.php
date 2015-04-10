<?php
namespace AMQPModule\Factory;

use AMQPModule\Publisher;
use PhpAmqpLib\Connection\AbstractConnection;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
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
        if(!empty($publisherConfig['exchange']) && is_string($publisherConfig['exchange'])) {
            $exchangeName = $this->configKey . '.exchanges.' . $publisherConfig['exchange'];
        }

        return new Publisher(
            $serviceLocator->get($exchangeName),
            $publisherConfig
        );
    }

}
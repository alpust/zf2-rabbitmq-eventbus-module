<?php
namespace EventBus\PortAdapter\ZF2\ServiceManager;

use Zend\ServiceManager\ServiceLocatorInterface;
use AMQPConnection;

class ConnectionAbstractFactory extends RabbitMQAbstractFactory
{

    protected $configSubKey = 'connections';

    protected $defaults = [
        'host' => 'localhost',
        'port' => 5672,
        'user' => 'guest',
        'password' => 'guest',
        'vhost' => '/'
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

        $connectionConfig = array_merge($this->defaults, $this->getServiceConfig($serviceLocator, $requested));

        return new AMQPConnection($connectionConfig);
    }


}
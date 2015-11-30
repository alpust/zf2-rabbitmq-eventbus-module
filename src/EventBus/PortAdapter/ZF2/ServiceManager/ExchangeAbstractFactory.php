<?php
namespace EventBus\PortAdapter\ZF2\ServiceManager;

use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ExchangeAbstractFactory
 * @package EventBus\PortAdapter\ZF2\ServiceManager
 */
class ExchangeAbstractFactory extends  RabbitMQAbstractFactory
{

    protected $configSubKey = 'exchanges';

    protected $defaults = [
        'name' => 'default_exchange',
        'type' => AMQP_EX_TYPE_DIRECT,
        'flags' => AMQP_NOPARAM,
        'arguments' => []
    ];

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return \AMQPExchange
     * @throws ServiceNotCreatedException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $requested = explode('.', $requestedName)[2];
        $exchangeConfig = array_merge($this->defaults, $this->getServiceConfig($serviceLocator, $requested));

        $exchangeConfig['name'] = $requested;

        $connectionName = $this->configKey . '.connections.default';
        if(isset($exchangeConfig['connection']) && is_string($exchangeConfig['connection'])) {
            $connectionName = $this->configKey . '.connections.' . $exchangeConfig['connection'];
        }
        $exchangeConfig['connection'] = $connectionName;

        return $exchangeConfig;
    }

}
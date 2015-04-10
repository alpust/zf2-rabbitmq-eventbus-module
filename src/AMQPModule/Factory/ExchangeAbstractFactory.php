<?php
namespace AMQPModule\Factory;


use Zend\ServiceManager\ServiceLocatorInterface;

class ExchangeAbstractFactory extends  AMQPAbstractFactory
{

    protected $configSubKey = 'exchanges';

    protected $defaults = [
        'name' => 'default_exchange',
        'type' => AMQP_EX_TYPE_DIRECT,
        'flags' => AMQP_PASSIVE,
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
        $exchangeConfig = array_merge($this->defaults, $this->getServiceConfig($serviceLocator, $requested));

        $connectionName = $this->configKey . '.connections.default';
        if(!empty($exchangeConfig['connection']) && is_string($exchangeConfig['connection'])) {
            $connectionName = $this->configKey . '.connections.' . $exchangeConfig['connection'];
        }
        /** @var \AMQPConnection $connection */
        $connection = $serviceLocator->get($connectionName);

        if(!$connection->isConnected()) {
            $connection->connect();
        }

        $channel = new \AMQPChannel($connection);

        $exchange = new \AMQPExchange($channel);
        $exchange->setName($exchangeConfig['name']);
        $exchange->setType($exchangeConfig['type']);
        $exchange->setArguments($exchangeConfig['arguments']);
        $exchange->setFlags($exchangeConfig['flags']);

        return $exchange;
    }

}
<?php
namespace EventBus\PortAdapter\ZF2\ServiceManager;

use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\ServiceLocatorInterface;

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

        $connectionPublisherName = $this->configKey . '.connections.defaultPublisher';
        if(isset($exchangeConfig['connectionPublisher']) && is_string($exchangeConfig['connectionPublisher'])) {
            $connectionPublisherName = $this->configKey . '.connections.' . $exchangeConfig['connectionPublisher'];
        }
        $exchangeConfig['connectionPublisher'] = $connectionPublisherName;

        $connectionSubscriberName = $this->configKey . '.connections.defaultSubscriber';
        if(isset($exchangeConfig['connectionSubscriber']) && is_string($exchangeConfig['connectionSubscriber'])) {
            $connectionSubscriberName = $this->configKey . '.connections.' . $exchangeConfig['connectionSubscriber'];
        }
        $exchangeConfig['connectionSubscriber'] = $connectionSubscriberName;

        return $exchangeConfig;
    }

}
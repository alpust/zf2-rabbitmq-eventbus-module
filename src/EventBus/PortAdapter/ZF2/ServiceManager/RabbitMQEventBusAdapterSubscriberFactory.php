<?php
namespace EventBus\PortAdapter\ZF2\ServiceManager;


use EventBus\PortAdapter\RabbitMQ\EventBusAdapterSubscriber;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RabbitMQEventBusAdapterSubscriberFactory
 * @package EventBus\PortAdapter\ZF2\ServiceManager
 */
class RabbitMQEventBusAdapterSubscriberFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return EventBusAdapterSubscriber
     * @throws ServiceNotCreatedException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if(empty($config['amqp']['boundedContext'])) {
            throw new ServiceNotCreatedException(
                "Please specify boundedContext in amqp config section. It should be application-dependable."
            );
        }

        $queueConfig = [
            'name' => $config['amqp']['boundedContext'],
            'flags' => AMQP_DURABLE
        ];

        $exchangeConfig = $serviceLocator->get('amqp.exchanges.messageBus');

        /** @var \AMQPConnection $connection */
        $connection = $serviceLocator->get($exchangeConfig['connection']);

        return new EventBusAdapterSubscriber($queueConfig, $exchangeConfig['name'], $connection);
    }


}
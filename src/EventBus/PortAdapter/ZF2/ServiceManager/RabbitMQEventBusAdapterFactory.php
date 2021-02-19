<?php
namespace EventBus\PortAdapter\ZF2\ServiceManager;


use EventBus\PortAdapter\RabbitMQ\EventBusAdapter;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RabbitMQEventBusAdapterFactory
 * @package EventBus\PortAdapter\ZF2\ServiceManager
 * @deprecated EventBusAdapter will soon deleted
 */
class RabbitMQEventBusAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return EventBusAdapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if(empty($config['amqp']['boundedContext'])) {
            throw new ServiceNotCreatedException('Please specify boundedContext in amqp config section. It should be application-dependable.');
        }

        $queueConfig = [
            'name' => $config['amqp']['boundedContext'],
            'flags' => AMQP_DURABLE,
            'arguments' => [],
        ];

        if (isset($config['amqp']['queue']) && is_array($config['amqp']['queue'])) {
            $queueConfig = array_merge_recursive($queueConfig, $config['amqp']['queue']);
        }

        $exchangeConfig = $serviceLocator->get('amqp.exchanges.messageBus');

        /** @var \AMQPConnection $connection */
        $connection = $serviceLocator->get($exchangeConfig['connection']);

        return new EventBusAdapter($queueConfig, $exchangeConfig, $connection);
    }


}

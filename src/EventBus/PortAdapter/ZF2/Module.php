<?php
namespace EventBus;

use EventBus\PortAdapter\ZF2\EventManager\Event as EventBusEvent;
use Zend\Console\Adapter\AdapterInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 * @package EventBus
 */
class Module implements
    ConfigProviderInterface,
    AutoloaderProviderInterface,
    ServiceProviderInterface,
    BootstrapListenerInterface,
    ConsoleUsageProviderInterface
{

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        if ($e instanceof MvcEvent) {
            $serviceManager = $e->getApplication()->getServiceManager();
            $config         = $serviceManager->get('Config');
            if (!empty($config['amqp']['boundedContext'])) {
                $boundedContext    = $config['amqp']['boundedContext'];
                $localEventManager = $e->getApplication()->getEventManager();

                /** @var \EventBus\Application\IEventBusInterface $eventBus */
                $eventBus = $serviceManager->get('EventBus');

                //We attaching handler with high priority, because we shouldn't be last subscriber or we return
                //bad result to some zend MVC processes
                $localEventManager->getSharedManager()->attach('*', '*', function (Event $event) use ($eventBus, $boundedContext) {
                    if ($event instanceof EventBusEvent || strpos($event->getName(), $boundedContext . '.') !== 0) {
                        return;
                    }
                    $eventBus->publish($event);
                }, 1000000);
            }
        }
    }


    protected $configFile = 'module.config.php';

    public function getModuleDir()
    {
        return __DIR__;
    }

    public function getModuleNamespace()
    {
        return __NAMESPACE__;
    }

    public function getConfig()
    {
        $config = include $this->getModuleDir() . '/config/' . $this->configFile;

        return $config;
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getServiceConfig()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'eventBus console [consume|testConsume|testPublish]:action [<message>]'
            => 'execute one of the commands',
            ['testConsume', 'Test connection and initialisation for consume'],
            ['testPublish', 'Test connection and initialisation for publishing'],
            ['message', 'message for publish test'],
            ['consume', 'Start consume messages with given configuration'],
        ];
    }
}
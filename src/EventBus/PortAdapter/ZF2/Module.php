<?php
namespace EventBus;

use Zend\EventManager\Event;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;

use EventBus\PortAdapter\ZF2\EventManager\Event as EventBusEvent;

class Module implements
    ConfigProviderInterface,
    AutoloaderProviderInterface,
    ServiceProviderInterface,
    BootstrapListenerInterface
{

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        if($e instanceof MvcEvent) {
            $serviceManager = $e->getApplication()->getServiceManager();
            $config = $serviceManager->get('Config');
            if(!empty($config['amqp']['boundedContext'])) {
                $boundedContext = $config['amqp']['boundedContext'];
                $localEventManager = $e->getApplication()->getEventManager();
                /** @var \EventBus\Application\IEventBusInterface $eventBus */
                $eventBus = $serviceManager->get('EventBus');
                $localEventManager->getSharedManager()->attach('*', '*', function(Event $event) use ($eventBus, $boundedContext){
                    if($event instanceof EventBusEvent || strpos($event->getName(), $boundedContext . '.') !== 0) {
                        return;
                    }
                    $eventBus->publish($event);
                });
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
}
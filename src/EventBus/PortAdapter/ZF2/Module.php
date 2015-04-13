<?php
namespace EventBus;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

class Module implements
    ConfigProviderInterface,
    AutoloaderProviderInterface,
    ServiceProviderInterface
{

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
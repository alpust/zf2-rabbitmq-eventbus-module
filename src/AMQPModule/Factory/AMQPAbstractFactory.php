<?php
namespace AMQPModule\Factory;


use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AMQPAbstractFactory implements AbstractFactoryInterface
{

    protected $configKey = 'amqp';

    protected $configSubKey;

    protected $config;

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $requested = explode('.', $requestedName);
        if (count($requested) !== 3 || $requested[0] !== $this->configKey || $requested[1] !== 'consumers') {
            return false;
        }

        $config = $this->getConfig($serviceLocator);
        if (empty($config)) {
            return false;
        }

        if (!isset($config[$this->configSubKey][$requested[2]])) {
            return false;
        }

        return true;
    }

    /**
     * @param ServiceLocatorInterface $services
     * @return array
     */
    protected function getConfig(ServiceLocatorInterface $services)
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (!$services->has('Config')) {
            $this->config = [];
            return $this->config;
        }

        $config = $services->get('Config');
        if (!isset($config[$this->configKey])) {
            $this->config = [];
            return $this->config;
        }

        $this->config = $config[$this->configKey];
        return $this->config;
    }


    protected function getServiceConfig(ServiceLocatorInterface $services, $name)
    {
        $amqpConfig = $this->getConfig($services);

        $serviceConfig = $amqpConfig[$this->configSubKey][$name];

        return $serviceConfig;
    }

}
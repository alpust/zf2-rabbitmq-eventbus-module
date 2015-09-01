<?php
namespace EventBus\Application;

/**
 * Interface IEventBusAdapterSubscriberInterface
 * @package EventBus\Application
 */
interface IEventBusAdapterSubscriberInterface
{

    /**
     * @param callable $callback
     * @return mixed
     */
    public function subscribe(callable $callback);

}
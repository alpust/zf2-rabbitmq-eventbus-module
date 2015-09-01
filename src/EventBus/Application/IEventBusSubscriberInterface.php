<?php
namespace EventBus\Application;

/**
 * Interface IEventBusSubscriberInterface
 * @package EventBus\Application
 */
interface IEventBusSubscriberInterface
{

    /**
     * @param callable $callback
     * @return mixed
     */
    public function subscribe(callable $callback);

}
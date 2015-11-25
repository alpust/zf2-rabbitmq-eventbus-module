<?php
namespace EventBus\Application;

/**
 * Interface IEventBusInterface
 * @package EventBus\Application
 * @deprecated This interface will be removed at nearest feature
 */
interface IEventBusAdapterInterface
{
    /**
     * @param array $event
     * @return mixed
     */
    public function publish($event = []);

    /**
     * @param callable $callback
     * @return mixed
     */
    public function subscribe(callable $callback);

}
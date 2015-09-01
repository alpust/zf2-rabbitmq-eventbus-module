<?php
namespace EventBus\Application;

/**
 * Interface IEventBusInterface
 * @package EventBus\Application
 * @deprecated This interface will be removed at nearest feature
 */
interface IEventBusAdapterInterface
{

    public function publish($event = []);

    public function subscribe(callable $callback);

}
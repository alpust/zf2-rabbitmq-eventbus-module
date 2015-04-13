<?php
namespace EventBus\Application;

/**
 * Interface IEventBusInterface
 * @package EventBus\Application
 */
interface IEventBusAdapterInterface
{

    public function publish($event = []);

    public function subscribe(callable $callback);

}
<?php
namespace EventBus\Application;

/**
 * Interface IEventBusAdapterPublisherInterface
 * @package EventBus\Application
 */
interface IEventBusAdapterPublisherInterface
{

    /**
     * @param $event
     * @return mixed
     */
    public function publish($event);

}
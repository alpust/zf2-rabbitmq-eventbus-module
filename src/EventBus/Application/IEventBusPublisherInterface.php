<?php
namespace EventBus\Application;

/**
 * Interface IEventBusPublisherInterface
 * @package EventBus\Application
 */
interface IEventBusPublisherInterface
{

    /**
     * @param array $event
     * @return mixed
     */
    public function publish($event = []);

}
<?php
namespace EventBus\Application;


/**
 * Interface IEventBusInterface
 * @package EventBus\Application
 */
interface IEventBusInterface
{

    /**
     * @return mixed
     */
    public function subscribe();

    /**
     * @param $event
     * @return mixed
     */
    public function publish($event);

}
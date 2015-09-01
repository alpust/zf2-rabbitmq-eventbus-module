<?php
namespace EventBus\Application;


/**
 * Interface IEventBusInterface
 * @package EventBus\Application
 * @deprecated This interface will be removed at nearest feature
 */
interface IEventBusInterface
{

    public function subscribe();

    public function publish($event);

}
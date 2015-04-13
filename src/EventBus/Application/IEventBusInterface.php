<?php
namespace EventBus\Application;


interface IEventBusInterface
{

    public function subscribe();

    public function publish($event);

}
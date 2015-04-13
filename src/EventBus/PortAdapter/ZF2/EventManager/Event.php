<?php
namespace EventBus\PortAdapter\ZF2\EventManager;

use EventBus\Application\ILocalEventInterface;
use Zend\EventManager\Event as ZendEvent;

class Event extends ZendEvent implements ILocalEventInterface
{

}
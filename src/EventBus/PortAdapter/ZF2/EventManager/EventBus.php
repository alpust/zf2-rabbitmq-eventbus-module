<?php
namespace EventBus\PortAdapter\ZF2\EventManager;

use EventBus\Application\IEventBusAdapterInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

class EventBus implements EventManagerAwareInterface
{

    use EventManagerAwareTrait;

    /** @var IEventBusAdapterInterface  */
    protected $eventBusAdapter;

    /** @var EventFactory  */
    protected $eventFactory;

    public function __construct(
        IEventBusAdapterInterface $eventBusAdapter,
        EventFactory $eventFactory
    ){
        $this->eventBusAdapter = $eventBusAdapter;
        $this->eventFactory = $eventFactory;
    }

    public function subscribe()
    {
        $callback = function($event){
            $this->getEventManager()->trigger($this->eventFactory->restore($event));
        };
        $callback->bindTo($this);
        $this->eventBusAdapter->subscribe($callback);
    }

    public function publish($event)
    {
        if(is_string($event)) {
            $event = [
                'name' => $event,
                'params' => []
            ];
        } elseif ($event instanceof EventInterface) {
            $event = [
                'name' => $event->getName(),
                'params' => $event->getParams()
            ];
        } elseif (!is_array($event) || !array_key_exists('name', $event) || !array_key_exists('params', $event)) {
            throw new \Exception('Can not publish. Incorrect event format.');
        }

        $this->eventBusAdapter->publish($event);
    }




}
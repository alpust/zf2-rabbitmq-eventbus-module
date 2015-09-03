<?php
namespace EventBus\PortAdapter\ZF2\EventManager;

use EventBus\Application\IEventBusAdapterSubscriberInterface;
use EventBus\Application\IEventBusAdapterPublisherInterface;
use EventBus\Application\Exception as EventBusException;

use EventBus\Application\IEventBusInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class EventBus
 * @package EventBus\PortAdapter\ZF2\EventManager
 */
class EventBus implements EventManagerAwareInterface, IEventBusInterface
{

    use EventManagerAwareTrait;

    /**
     * @var IEventBusAdapterSubscriberInterface
     */
    protected $eventBusAdapterSubscriber;

    /**
     * @var IEventBusAdapterPublisherInterface
     */
    protected $eventBusAdapterPublisher;

    /**
     * @var EventFactory
     */
    protected $eventFactory;

    /**
     * @param IEventBusAdapterSubscriberInterface $eventBusAdapterSubscriber
     * @param IEventBusAdapterPublisherInterface $eventBusAdapterPublisher
     * @param EventFactory $eventFactory
     */
    public function __construct(
        IEventBusAdapterSubscriberInterface $eventBusAdapterSubscriber,
        IEventBusAdapterPublisherInterface $eventBusAdapterPublisher,
        EventFactory $eventFactory
    ){
        $this->eventBusAdapterSubscriber = $eventBusAdapterSubscriber;
        $this->eventBusAdapterPublisher = $eventBusAdapterPublisher;
        $this->eventFactory = $eventFactory;
    }

    /**
     * Subscribe all internal listeners to external events
     */
    public function subscribe()
    {
        $callback = function($event){
            $this->getEventManager()->trigger($this->eventFactory->restore($event));
        };
        $callback->bindTo($this);
        try {
            $this->eventBusAdapterSubscriber->subscribe($callback);
        } catch(EventBusException $e) {

            //Trigger event with exception details
            $this->getEventManager()->trigger(
                'eventBus.exception', null,
                ['message' => $e->getMessage(), 'trace' => $e->getTrace()]
            );
        }

        return false;
    }

    /**
     * Publish internal event to external systems
     * @param $event
     * @throws \Exception
     * @return boolean
     */
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

        try {
            return $this->eventBusAdapterPublisher->publish($event);
        } catch(EventBusException $e) {

            //Trigger event with exception details
            $this->getEventManager()->trigger(
                'eventBus.exception', null,
                ['message' => $e->getMessage(), 'trace' => $e->getTrace()]
            );
        }

        return false;
    }




}
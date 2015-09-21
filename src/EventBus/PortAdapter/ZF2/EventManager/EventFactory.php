<?php
namespace EventBus\PortAdapter\ZF2\EventManager;

class EventFactory
{

    public function restore($event)
    {

        if(is_string($event)) {
            return new Event($event);
        } elseif (is_array($event)) {
            if(!array_key_exists('name', $event) || !array_key_exists('params', $event)) {
                throw new \Exception('Can\'t restore event from ' . implode(':', $event));
            }

            if(isset($event['publishedAt'])) {
                $event['params']['publishedAt'] = $event['publishedAt'];
            }

            return new Event($event['name'], null, $event['params']);
        }
        throw new \Exception('Unrecognized event structure.');
    }

}
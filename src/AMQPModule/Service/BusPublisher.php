<?php


namespace AMQPModule\Service;


class BusPublisher
{

    /** @var \AMQPExchange  */
    protected $exchange;

    function __construct(
        \AMQPExchange $exchange
    ){
        $this->exchange = $exchange;
    }

    public function publish($eventName, $message)
    {
        try {
            list($message, $attributes) = $this->stringifyMessage($message);
            return $this->exchange->publish($message, $eventName, AMQP_NOPARAM, $attributes);
        } catch(\Exception $e) {
            return false;
        }

    }

    protected function stringifyMessage($message)
    {
        if(is_string($message)) {
            return [$message, ['content_type' => 'text/plain']];
        } elseif(is_array($message)) {
            return [serialize($message), ['content_type' => 'serialized/array']];
        }

        throw new \Exception('Unsupported message type');

    }

}
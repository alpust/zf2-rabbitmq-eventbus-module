<?php
namespace EventBus\PortAdapter\RabbitMQ;

use EventBus\Application\IEventBusAdapterInterface;

class EventBusAdapter implements IEventBusAdapterInterface
{

    /** @var \AMQPQueue  */
    protected $queue;

    /** @var \AMQPExchange  */
    protected $exchange;

    /** @var   */
    protected $callback;

    protected $messageAttributes = [
        'delivery_mode' => 2,
        'content_encoding' => 'UTF8'
    ];

    public function __construct(
        \AMQPQueue $queue,
        \AMQPExchange $exchange
    ){
        $this->queue = $queue;
        $this->exchange = $exchange;
    }

    public function publish($event = [])
    {
        try {
            list($message, $attributes) = $this->prepareMessage($event);
            return $this->exchange->publish($message, null, AMQP_NOPARAM, $attributes);
        } catch(\Exception $e) {
            return false;
        }
    }

    protected function prepareMessage($message)
    {
        $attributes = $this->messageAttributes;

        if(is_string($message)) {
            $attributes['content_type'] = 'text/plain';
            return [$message, $attributes];
        } elseif(is_array($message)) {
            $attributes['content_type'] = 'serialized/array';
            return [serialize($message), $attributes];
        }

        throw new \Exception('Unsupported message type');

    }

    public function subscribe(callable $callback)
    {
        $this->callback = $callback;

        if(!$this->queue->bind($this->exchange->getName())) {
            throw new \Exception('Can not bind ' . $this->queue->getName() . ' to an exchange ' . $this->exchange->getName());
        }
        $callback = function(\AMQPEnvelope $message){
            try {
                call_user_func($this->callback, $this->unpackMessage($message));
                $this->queue->ack($message->getDeliveryTag());
            } catch (\Exception $e) {
                $this->processFailedSubscription($message);
            }
        };
        $callback->bindTo($this);

        $this->queue->consume($callback);
    }

    protected function processFailedSubscription(\AMQPEnvelope $message)
    {

        $attempt = $message->getHeader('redelivery_counter') ? $message->getHeader('redelivery_counter') : 1;
        if($attempt < 3) {
            $headers = $message->getHeaders();
            $headers['redelivery_counter'] = ++$attempt;
            $attributes = array_merge(
                $this->messageAttributes,
                [
                    'content_type' => $message->getContentType(),
                    'headers' => $headers
                ]
            );

            $this->exchange->publish($message->getBody(), $message->getRoutingKey(), AMQP_NOPARAM, $attributes);
        }
        $this->queue->ack($message->getDeliveryTag());
    }

    protected function unpackMessage(\AMQPEnvelope $message)
    {
        if($message->getContentType() == 'text/plain') {
            return $message->getBody();
        } elseif ($message->getContentType() == 'serialized/array') {
            return unserialize($message->getBody());
        }

        throw new \Exception('Unsupported message content type ' . $message->getContentType());
    }

}
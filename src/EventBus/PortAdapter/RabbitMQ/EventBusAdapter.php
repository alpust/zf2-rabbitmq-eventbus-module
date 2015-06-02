<?php
namespace EventBus\PortAdapter\RabbitMQ;

use EventBus\Application\IEventBusAdapterInterface;

class EventBusAdapter implements IEventBusAdapterInterface
{

    /** @var  array */
    protected $queueConfig;

    /** @var   */
    protected $callback;

    /** @var  array */
    protected $exchangeConfig;

    /** @var \AMQPQueue  */
    private $queue;

    /** @var \AMQPExchange  */
    private $exchange;

    /** @var  \AMQPChannel */
    private $channel;

    /** @var \AMQPConnection  */
    private $connection;

    protected $messageAttributes = [
        'delivery_mode' => 2,
        'content_encoding' => 'UTF8'
    ];

    public function __construct(
        $queueConfig,
        $exchangeConfig,
        \AMQPConnection $connection
    ){
        $this->queueConfig = $queueConfig;
        $this->exchangeConfig = $exchangeConfig;
        $this->connection = $connection;
    }

    function __destruct()
    {
        $this->getExchange()->getConnection()->disconnect();
    }

    public function publish($event = [])
    {
        try {
            list($message, $attributes) = $this->prepareMessage($event);
            $result = $this->getExchange()->publish($message, null, AMQP_NOPARAM, $attributes);
            $this->getChannel()->getConnection()->disconnect();
            return $result;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function subscribe(callable $callback)
    {
        $this->callback = $callback;

        if(!$this->getQueue()->bind($this->getExchange()->getName())) {
            throw new \Exception('Can not bind ' . $this->getQueue()->getName() . ' to an exchange ' . $this->getExchange()->getName());
        }
        $callback = function(\AMQPEnvelope $message){
            try {
                if($message->getAppId() !== $this->getQueue()->getName()) {
                    call_user_func($this->callback, $this->unpackMessage($message));
                }
                $this->getQueue()->ack($message->getDeliveryTag());
            } catch (\Exception $e) {
                $this->processFailedSubscription($message);
            }
        };
        $callback->bindTo($this);

        $this->getQueue()->consume($callback);
    }

    protected function prepareMessage($message)
    {
        $attributes = $this->messageAttributes;
        $attributes['app_id'] = $this->getQueue()->getName();

        if(is_string($message)) {
            $attributes['content_type'] = 'text/plain';
            return [$message, $attributes];
        } elseif(is_array($message)) {
            $attributes['content_type'] = 'serialized/array';
            return [serialize($message), $attributes];
        }

        throw new \Exception('Unsupported message type');

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

            $this->getExchange()->publish($message->getBody(), $message->getRoutingKey(), AMQP_NOPARAM, $attributes);
        }
        $this->getQueue()->ack($message->getDeliveryTag());
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

    /**
     * @return \AMQPQueue
     */
    protected function getQueue()
    {
        if(!$this->queue) {
            $this->queue = new \AMQPQueue($this->getChannel());
            $this->queue->setName($this->queueConfig['name']);
            $this->queue->setFlags($this->queueConfig['flags']);
            $this->queue->declareQueue();
        }

        return $this->queue;
    }

    /**
     * @return \AMQPExchange
     * @throws \Exception
     */
    protected function getExchange()
    {

        if(!$this->exchange) {

            $this->exchange = new \AMQPExchange($this->getChannel());
            $this->exchange->setName($this->exchangeConfig['name']);
            $this->exchange->setType($this->exchangeConfig['type']);
            $this->exchange->setArguments($this->exchangeConfig['arguments']);
            $this->exchange->setFlags($this->exchangeConfig['flags']);

            if(!$this->exchange->declareExchange()) {
                throw new \Exception('Can not declare exchange ' . $this->exchange->getName());
            }
        }

        return $this->exchange;
    }

    private function getChannel()
    {
        if(!$this->channel) {
            if(!$this->connection->isConnected()) {
                $this->connection->connect();
            }

            $this->channel = new \AMQPChannel($this->connection);
        }

        return $this->channel;
    }

}
<?php
namespace EventBus\PortAdapter\RabbitMQ;

use EventBus\Application\IEventBusAdapterInterface;

/**
 * Class EventBusAdapter
 * @package EventBus\PortAdapter\RabbitMQ
 */
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

    /**
     * @param $queueConfig
     * @param $exchangeConfig
     * @param \AMQPConnection $connection
     */
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

    /**
     * @param array $event
     * @return bool
     */
    public function publish($event = [])
    {
        try {

            if(!$this->connection->isConnected()) {
                $this->connection->connect();
            }

            list($message, $attributes) = $this->prepareMessage($event);
            $result = $this->getExchange()->publish($message, null, AMQP_NOPARAM, $attributes);
            $this->connection->disconnect();
            return $result;
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * @param callable $callback
     * @return mixed|void
     * @throws \Exception
     */
    public function subscribe(callable $callback)
    {
        $this->callback = $callback;

        if(!$this->connection->isConnected()) {
            $this->connection->connect();
        }

        if(!$this->getQueue()->bind($this->getExchange()->getName())) {
            throw new \Exception('Can not bind ' . $this->getQueue()->getName() . ' to an exchange ' . $this->getExchange()->getName());
        }

        $callback = function(\AMQPEnvelope $message){
            try {
                switch ($message->getType()) {
                    case 'eventBus.consumer-stop':
                        $this->getQueue()->ack($message->getDeliveryTag());
                        call_user_func($this->callback, 'eventBus.consumer-stop');
                        $this->stopConsumer();
                        break;
                    default:
                        if($message->getAppId() !== $this->getQueue()->getName()) {
                            call_user_func($this->callback, $this->unpackMessage($message));
                        }
                }

                if(!$this->connection->isConnected()) {
                    $this->connection->connect();
                }

                $this->getQueue()->ack($message->getDeliveryTag());
            } catch (\Exception $e) {
                $this->processFailedSubscription($message);
            }
        };
        $callback->bindTo($this);

        $this->getQueue()->consume($callback);
    }

    /**
     * @param $message
     * @return array
     * @throws \Exception
     */
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

    /**
     * @param \AMQPEnvelope $message
     * @throws \Exception
     */
    protected function processFailedSubscription(\AMQPEnvelope $message)
    {

        if(!$this->connection->isConnected()) {
            $this->connection->connect();
        }

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

    /**
     * @param \AMQPEnvelope $message
     * @return mixed|string
     * @throws \Exception
     */
    protected function unpackMessage(\AMQPEnvelope $message)
    {
        switch ($message->getContentType()) {
            case 'text/plain':
                return $message->getBody();
            case 'serialized/array':
                return unserialize($message->getBody());
            default:
                throw new \Exception('Unsupported message content type ' . $message->getContentType());
        }
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
            $this->queue->setArguments($this->queueConfig['arguments']);

            if(!$this->connection->isConnected()) {
                $this->connection->connect();
            }

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

            if(!$this->connection->isConnected()) {
                $this->connection->connect();
            }

            if(!$this->exchange->declareExchange()) {
                throw new \Exception('Can not declare exchange ' . $this->exchange->getName());
            }
        }

        return $this->exchange;
    }

    /**
     * @return \AMQPChannel
     */
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

    private function stopConsumer()
    {
        $this->getChannel()->getConnection()->disconnect();
        exit("\nconsumer-stop signal received\n");
    }

}

<?php
namespace EventBus\PortAdapter\RabbitMQ;

use EventBus\Application\IEventBusAdapterSubscriberInterface;
use EventBus\Application\NotSubscribedListenerException;

/**
 * Class EventBusAdapterSubscriber
 * @package EventBus\PortAdapter\RabbitMQ
 */
class EventBusAdapterSubscriber implements IEventBusAdapterSubscriberInterface
{

    /**
     * @var array
     */
    protected $queueConfig;

    /**
     * @var Callback
     */
    protected $callback;

    /**
     * @var string
     */
    protected $exchangeConfig;

    /** @var \AMQPQueue  */
    private $queue;

    /** @var  \AMQPChannel */
    private $channel;

    /** @var \AMQPConnection  */
    private $connection;

    /**
     * @var array
     */
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

    public function __destruct()
    {
        //$this->connection->disconnect();
    }

    /**
     * @param callable $callback
     * @throws NotSubscribedListenerException
     * @throws \Exception
     * @return void
     */
    public function subscribe(callable $callback)
    {
        $this->callback = $callback;

        //Check whether connection open or not
        if(!$this->connection->isConnected()) {
            $this->connection->connect();
        }


        /** Declare exchange. In case it is already exists it could change it's options */
        $this->declareExchange();

        //Binding queue to correct exchange
        if(!$this->getQueue()->bind($this->exchangeConfig['name'])) {
            throw new NotSubscribedListenerException("Can not bind " . $this->getQueue()->getName() . " to an exchange " . $this->exchangeConfig);
        }

        $callback = function(\AMQPEnvelope $message){

            switch ($message->getType()) {
                //Stop consumer by special event
                case 'eventBus.consumer-stop':
                    $this->getQueue()->ack($message->getDeliveryTag());
                    call_user_func($this->callback, 'eventBus.consumer-stop');
                    $this->stopConsumer();
                    break;
                default:

                    //If publisher is same BC as subscriber, don't proceed
                    if($message->getAppId() !== $this->getQueue()->getName()) {
                        call_user_func($this->callback, $this->unpackMessage($message));
                    }
            }

            //Answering to RabbitMQ, that event processed
            $this->getQueue()->ack($message->getDeliveryTag());
        };

        $callback->bindTo($this);

        //Listen events
        $this->getQueue()->consume($callback);
    }

    protected function declareExchange()
    {
        $exchange = new \AMQPExchange($this->getChannel());
        $exchange->setName($this->exchangeConfig['name']);
        $exchange->setType($this->exchangeConfig['type']);
        $exchange->setArguments($this->exchangeConfig['arguments']);
        $exchange->setFlags($this->exchangeConfig['flags']);

        if(!$exchange->declareExchange()) {
            throw new \Exception('Can not declare exchange ' . $exchange->getName());
        }
    }

    /**
     * Unserialize message
     * @param \AMQPEnvelope $message
     * @return mixed
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
            $this->queue->declareQueue();
        }

        return $this->queue;
    }

    /**
     * @return \AMQPChannel
     */
    private function getChannel()
    {
        if(!$this->channel) {
            $this->channel = new \AMQPChannel($this->connection);
        }

        return $this->channel;
    }

    private function stopConsumer()
    {
        $this->connection->disconnect();
        exit("\nconsumer-stop signal received\n");
    }

}

<?php
namespace EventBus\PortAdapter\RabbitMQ;

use EventBus\Application\IEventBusAdapterPublisherInterface;
use EventBus\Application\NotDeliveredEventException;

/**
 * Class EventBusAdapterPublisher
 * @package EventBus\PortAdapter\RabbitMQ
 */
class EventBusAdapterPublisher implements IEventBusAdapterPublisherInterface
{
    /**
     * @var array
     */
    protected $exchangeConfig;

    /**
     * @var string
     */
    private $queueName;

    /** @var \AMQPExchange  */
    private $exchange;

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
     * @param $exchangeConfig
     * @param $queueName
     * @param \AMQPConnection $connection
     */
    public function __construct(
        $exchangeConfig,
        $queueName,
        \AMQPConnection $connection
    ){
        $this->queueName = $queueName;
        $this->exchangeConfig = $exchangeConfig;
        $this->connection = $connection;
    }

    public function __destruct()
    {
        $this->exchange = null;
    }

    /**
     * @param $event
     * @return mixed
     * @throws NotDeliveredEventException
     */
    public function publish($event)
    {
        try {

            //Prepare message for sending to RabbitMQ
            list($message, $attributes) = $this->prepareMessage($event);

            //Check connection, if no reconnect to RabbitMQ
            if(!$this->connection->isConnected()) {
                $this->connection->connect();
            }

            $result = $this->getExchange()->publish($message, null, AMQP_NOPARAM, $attributes);

            $this->exchange = null;
            //$this->channel = null;

            return $result;

        } catch(\Exception $e) {

            throw new NotDeliveredEventException("Event not delivered to queue", 0, $e);
        }
    }

    /**
     * Serialize message for send to RabbitMQ queue
     * @param $message
     * @return array
     * @throws \Exception
     */
    protected function prepareMessage($message)
    {
        $attributes = $this->messageAttributes;
        $attributes['app_id'] = $this->queueName;

        if(is_string($message)) {
            $attributes['content_type'] = 'text/plain';
            return [$message, $attributes];
        } elseif(is_array($message)) {
            $message['publishedAt'] = date(DATE_ATOM);
            $attributes['content_type'] = 'serialized/array';
            return [serialize($message), $attributes];
        }

        throw new \Exception('Unsupported message type');

    }

    /**
     * @return \AMQPExchange
     * @throws \Exception
     */
    protected function getExchange()
    {

        if(!$this->exchange) {

            try {
                $this->exchange = new \AMQPExchange($this->getChannel());
            } catch(\Exception $e) {
                $this->channel = null;
                $this->exchange = new \AMQPExchange($this->getChannel());
            }

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

}
<?php
return [
    'amqp' => [
        'connections' => [
            'default' => [
                'host' => 'localhost',
                'port' => 5672,
                'user' => 'guest',
                'password' => 'guest',
                'vhost' => '/'
            ]
        ],
        'exchanges' => [
            'default' => [
                'connection' => 'default',
                'type' => AMQP_EX_TYPE_DIRECT,
                'flags' => AMQP_NOPARAM,
                'arguments' => []
            ],
            'messageBus' => [
                'connection' => 'default',
                'type' => AMQP_EX_TYPE_TOPIC,
            ]
        ],
        'queues' => [
            'default' => [
                'exchange' => 'default',
                'routing_key' => null,
                'flags' => AMQP_NOPARAM,
                'arguments' => []
            ]
        ],
        'consumers' => [
            'default' => [
                'queue' => 'default',
                'flags' => AMQP_AUTOACK,
                'tag' => null
            ]
        ],
        'publishers' => [
            'default' => [
                'exchange' => 'default',
                'routing_key' => null,
                'flags' => AMQP_NOPARAM,
                'attributes' => [

                ]
            ]
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'AMQPModule\ServiceManager\Factory\ConnectionAbstractFactory',
            'AMQPModule\ServiceManager\Factory\ExchangeAbstractFactory',
            'AMQPModule\ServiceManager\Factory\QueueAbstractFactory',
            'AMQPModule\ServiceManager\Factory\ConsumerAbstractFactory',
            'AMQPModule\ServiceManager\Factory\PublisherAbstractFactory'
        ],
        'factories' => [
            'amqp.messageBus.publisher' => 'AMQPModule\Service\BusPublisherFactory',
            'amqp.messageBus.consumer' => 'AMQPModule\Service\BusConsumerFactory'
        ],
        'initializers' => [
            'AMQPInitializer' => new \AMQPModule\ServiceManager\AMQPInitializer(),
        ]
    ],
];
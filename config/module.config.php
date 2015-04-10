<?php
return [
    'amqp' => [
        'connections' => [
            'default' => [
                'host' => 'localhost',
                'port' => 5672,
                'user' => 'guest',
                'password' => 'guest'
            ]
        ],
        'exchanges' => [
            'default' => [
                'connection' => 'default',
                'name' => 'default_exchange',
                'type' => AMQP_EX_TYPE_DIRECT,
                'flags' => AMQP_PASSIVE,
                'arguments' => []
            ]
        ],
        'queues' => [
            'default' => [
                'exchange' => 'default',
                'name' => 'default_queue',
                'flags' => AMQP_PASSIVE,
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
            'AMQPModule\Factory\ConnectionAbstractFactory',
            'AMQPModule\Factory\ExchangeAbstractFactory',
            'AMQPModule\Factory\QueueAbstractFactory',
            'AMQPModule\Factory\ConsumerAbstractFactory',
            'AMQPModule\Factory\PublisherAbstractFactory'
        ],
        'initializers' => [
            'AMQPInitializer' => new \AMQPModule\AMQPInitializer(),
        ]
    ],
];
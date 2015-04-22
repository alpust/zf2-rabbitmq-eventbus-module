<?php
return [
    'console' => [
        'router' => [
            'routes' => [
                'eventBusTest'   => [
                    'type'    => 'simple',
                    'options' => [
                        'route'    => 'eventBus console [consume|testConsume|testPublish]:action [<message>]',
                        'defaults' => [
                            'controller' => 'EventBusConsoleController',
                            'action' => 'consume'
                        ]
                    ]
                ],
            ],
        ]
    ],
    'controllers' => [
        'invokables' => [
            'EventBusConsoleController' => 'EventBus\PortAdapter\ZF2\Controller\ConsoleController'
        ]
    ],
    'amqp' => [
        'boundedContext' => '',
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
            'messageBus' => [
                'connection' => 'default',
                'type' => AMQP_EX_TYPE_FANOUT,
                'flags' => AMQP_DURABLE
            ]
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'EventBus\PortAdapter\ZF2\ServiceManager\ConnectionAbstractFactory',
            'EventBus\PortAdapter\ZF2\ServiceManager\ExchangeAbstractFactory',

        ],
        'factories' => [
            'eventBus.portAdapter.rabbitMQ.adapter' => 'EventBus\PortAdapter\ZF2\ServiceManager\RabbitMQEventBusAdapterFactory',
            'EventBus' => 'EventBus\PortAdapter\ZF2\EventManager\EventBusFactory'
        ],
    ],
];
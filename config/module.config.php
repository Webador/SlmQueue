<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'SlmQueue\Queue\QueuePluginManager' => 'SlmQueue\Factory\QueuePluginManagerFactory',
            'SlmQueue\Worker\Beanstalk\Worker'  => 'SlmQueue\Factory\BeanstalkWorkerFactory',
            'SlmQueue\Worker\Sqs\Worker'        => 'SlmQueue\Factory\SqsWorkerFactory'
        )
    ),

    'console'   => array(
        'router' => array(
            'routes' => array(
                'slmqueue-worker' => array(
                    'type'    => 'Simple',
                    'options' => array(
                        'route'    => 'queue (beanstalk|sqs):system [--queueName=] --start',
                        'defaults' => array(
                            'controller' => 'SlmQueue\Controller\BeanstalkWorkerController',
                            'action'     => 'process'
                        ),
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'SlmQueue\Controller\Worker' => 'SlmQueue\Controller\WorkerController'
        )
    ),

    'slm_queue' => array(
        /**
         * Parameters for the worker
         */
        'worker' => array(
            'max_runs'   => 100000,
            'max_memory' => 1024
        ),

        /**
         * Queues configuration
         */
        'queues' => array(),

        /**
         * Beanstalk configuration
         */
        'beanstalk' => array(
            'default_tube' => 'default',

            'connection'   => array(
                'host'    => '0.0.0.0',
                'port'    => 11300,
                'timeout' => 2
            )
        ),

        /**
         * Amazon SQS configuration
         */
        'sqs' => array(
            'default_queue' => 'default',

            'aws_config'    => '',

            /*'connection'    => array(
                'key'    => '',
                'secret' => '',
                'region' => ''
            )*/
        ),
    ),
);

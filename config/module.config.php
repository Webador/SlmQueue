<?php

return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'slmqueue-worker' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route'    => 'queue --start',
                        'defaults' => array(
                            'controller' => 'SlmQueue\Controller\WorkerController',
                            'action'     => 'reserve'
                        ),
                    ),
                ),
            ),
        ),
    ),

    'pheanstalk' => array(
        'host'               => '0.0.0.0',
        'port'               => '',
        'connection_timeout' => '',
    ),

    'slm_queue' => array(
        'max_runs'   => 100000,
        'max_memory' => 1024,
    ),
);

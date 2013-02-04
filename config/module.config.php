<?php

return array(
    'console'   => array(
        'router' => array(
            'routes' => array(
                'slmqueue-worker' => array(
                    'type'    => 'simple',
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

    'slm_queue' => array(
        'connection' => array(
            'host'    => '0.0.0.0',
            'port'    => 11300,
            'timeout' => 2
        ),

        'tubes'      => array(
            'ignore' => '',
            'watch'  => '',
            'use'    => ''
        ),

        'max_runs'   => 100000,
        'max_memory' => 1024
    ),
);

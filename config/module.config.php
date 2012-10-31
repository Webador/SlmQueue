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

    'slm_queue' => array(
        'host'               => '0.0.0.0',
        'port'               => '',
        'connection_timeout' => ''
    ),
);

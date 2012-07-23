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

    'controllers' => array(
        'invokables' => array(
            'SlmQueue\Controller\WorkerController' => 'SlmQueue\Controller\WorkerController'
        ),
    ),
);
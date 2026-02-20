<?php

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
    'routes' => [
        'home' => [
            'type'    => Literal::class,
            'options' => [
                'route'    => '/',
                'defaults' => [
                    'controller' => Controller\IndexController::class,
                    'action'     => 'index',
                ],
            ],
        ],

        'application' => [
            'type'    => Literal::class,          
            'options' => [
                'route'    => '/application',
                'defaults' => [
                    'controller' => Controller\IndexController::class,
                    'action'     => 'index',
                ],
            ],
            'may_terminate' => true,              
            'child_routes' => [                   
                'default' => [                    
                    'type'    => Segment::class,
                    'options' => [
                        'route'    => '[/:action]',
                        'constraints' => [
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        ],
                        'defaults' => [
                            'action' => 'index',
                        ],
                    ],
                ],
                'editUsuario' => [                    
                    'type'    => Segment::class,
                    'options' => [
                        'route'    => '/edit-usuario[/:id]',
                        'constraints' => [
                            'id'     => '[0-9]+',     
                        ],
                        'defaults' => [
                            'action' => 'editUsuario',
                        ],
                    ],
                ],
                'updateUsuario' => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/update-usuario',
                        'defaults' => [
                            'action' => 'updateUsuario',
                        ],
                    ],
                ],
            ],
        ],
    ],
],

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Factory\IndexControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy',
        ],
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];

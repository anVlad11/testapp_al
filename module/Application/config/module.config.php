<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Router\Http\Method;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\DBAL\Driver\PDOPgSql\Driver as PDOPgSqlDriver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

error_reporting(E_ALL);
ini_set('display_errors', true);
return [
    'router' => [
        'routes' => [
            'application' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api/tasks[/[:id[/]]]',
                    'constraints' => [
                        'id' => '[0-9a-f]{8}\-?[0-9a-f]{4}\-?4[0-9a-f]{3}\-?[89ab][0-9a-f]{3}\-?[0-9a-f]{12}'
                    ],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'get'
                    ],
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'app_get' => [
                        'type' => Method::class,
                        'options' => [
                            'verb' => 'get',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action' => 'get'
                            ]
                        ],

                    ],
                    'app_post' => [
                        'type' => Method::class,
                        'options' => [
                            'verb' => 'post',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action' => 'post'
                            ]
                        ]
                    ],
                    'app_put' => [
                        'type' => Method::class,
                        'options' => [
                            'verb' => 'put',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action' => 'put'
                            ]
                        ]
                    ]
                ],
            ]
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy'
        ],
    ],

    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ],
        'connection' => [
            'orm_default' => [
                'driverClass' => PDOPgSqlDriver::class ,
                'params'      => [
                    'host'     => 'localhost',
                    'port'     => '5432',
                    'user'     => 'taskman',
                    'password' => 'taskman',
                    'dbname'   => 'taskman',
                ]
            ]
        ],
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'data/Migrations',
                'name'      => 'Doctrine Database Migrations',
                'namespace' => 'Migrations',
                'table'     => 'migrations',
            ],
        ],
    ],
];

<?php

declare(strict_types=1);

namespace Application;

use Application\Controller\Factory\IndexControllerFactory;
use Application\Controller\Factory\ShortenedUrlControllerFactory;
use Application\Controller\Factory\UserControllerFactory;
use Application\Entity\User;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Application\Controller\Factory\AuthControllerFactory;
/*
 *  Routes File for different routes.
 *
 * */
return [
    'router' => [

        'routes' => [
            'users' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/users[/:page_number]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page_number' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        '__NAMESPACE__' => 'Application\Controller',
                        'action' => 'index',
                    ],

                ],
                // The following allows "/users" to match on its own if no child routes match:
                'may_terminate' => true,
                'child_routes' => [
                    'create' => [
                        'type' => literal::class,
                        'options' => [
                            'route' => '/create',
                            'defaults' => [
                                'controller' => Controller\UserController::class,
                                '__NAMESPACE__' => 'Application\Controller',
                                'action' => 'create',
                            ],
                        ],

                    ],

                ],

            ],

            //Shortened URLs
            'shortenedurls' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/shortenedurls[/:page_number]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page_number' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ShortenedUrlController::class,
                        '__NAMESPACE__' => 'Application\Controller',
                        'action' => 'index',
                    ],

                ],
                // The following allows "/users" to match on its own if no child routes match:
                'may_terminate' => true,
                'child_routes' => [
                    //Create
                    'create' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/create',
                            'defaults' => [
                                'controller' => Controller\ShortenedUrlController::class,
                                '__NAMESPACE__' => 'Application\Controller',
                                'action' => 'create',
                            ],
                        ],
                    ],

                    //View
                    'view' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/view[/:id]',
                            'defaults' => [
                                'controller' => Controller\ShortenedUrlController::class,
                                '__NAMESPACE__' => 'Application\Controller',
                                'action' => 'view',
                            ],
                        ],
                    ],

                    //Edit
                    'edit' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/edit[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => Controller\ShortenedUrlController::class,
                                '__NAMESPACE__' => 'Application\Controller',
                                'action' => 'edit',
                            ],
                        ],
                    ],

                    //Delete
                    'delete' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/delete[/:id[/:mode]]',
                            'constraints' => [
                                'id' => '[0-9]+',  // Assuming id is numeric
                                'mode' => '[a-zA-Z0-9_-]+',
                            ],
                            'defaults' => [
                                'controller' => Controller\ShortenedUrlController::class,
                                '__NAMESPACE__' => 'Application\Controller',
                                'action' => 'delete',
                            ],
                        ],
                    ],

                    //listTrash
                    'listTrash' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/listTrash',
                            'defaults' => [
                                'controller' => Controller\ShortenedUrlController::class,
                                '__NAMESPACE__' => 'Application\Controller',
                                'action' => 'listTrash',
                            ],
                        ],
                    ],

                    //restore
                    'restore' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/restore[/:id]',
                            'defaults' => [
                                'controller' => Controller\ShortenedUrlController::class,
                                '__NAMESPACE__' => 'Application\Controller',
                                'action' => 'restore',
                            ],
                        ],
                    ],

                    //redirect
                    'redirect' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/redirect[/:id]',
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => Controller\ShortenedUrlController::class,
                                '__NAMESPACE__' => 'Application\Controller',
                                'action' => 'redirect',
                            ],
                        ],
                    ],

                ],

            ],

            'login' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],

            'logout' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],

            'root' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],

            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/home',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],

        ],
    ],

    'doctrine' => [

        'driver' => [
            'application_entities' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [  __DIR__ . '/../src/Entity']

            ],
            'orm_default' => [
                'drivers' => [
                    'Application\Entity' => 'application_entities'
                ]
            ]
        ],

        'authentication' => [

            'orm_default' => [
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Application\Entity\User',
                'identity_property' => 'userName',
                'credential_property' => 'password',
                'credential_callable' => function (User $user, $passwordGiven) {
                    return User::hashPassword($user, $passwordGiven);
                }
            ]

        ],
        'doctrine_factories' => [
            'authenticationadapter' => 'Auth\Adapter\AuthAdapterFactory'
        ]


    ],

    'doctrine_factories' => [
        'authenticationadapter' => 'Auth\Adapter\AuthAdapterFactory'
    ],

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => IndexControllerFactory::class,
            Controller\UserController::class => UserControllerFactory::class,
            Controller\AuthController::class => AuthControllerFactory::class,
            Controller\ShortenedUrlController::class => ShortenedUrlControllerFactory::class,
        ],
    ],

    'service_manager' => [
        'invokables' => [
            'Application\Listener\CreateAdminUserListener' => 'Application\Listener\CreateAdminUserListener',
        ],
        'listeners' => [
            'Application\Listener\CreateAdminUserListener',
        ],
        'abstract_factories' => [
            'Laminas\Cache\Service\StorageCacheAbstractServiceFactory',
        ],
        'factories' => [
            'Application\Entity\Repository\ShortenedUrlRepository' =>
                'Application\Entity\Repository\Factory\ShortenedUrlRepositoryFactory',
            'Application\Form\ShortenedUrlForm' => 'Application\Form\ShortenedUrlFormFactory',
            'Application\Service\ShortenedUrlService' => 'Application\Service\Factory\ShortenedUrlServiceFactory',
            'Application\Service\UserService' => 'Application\Service\Factory\UserServiceFactory',
            'Application\Service\AuthService' => 'Application\Service\Factory\AuthServiceFactory',
            'Application\Service\AuthAdapter' => 'Application\Service\Factory\AuthAdapterFactory',
        ],
    ],

    'view_manager' => [
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

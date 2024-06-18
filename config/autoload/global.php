<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
use Laminas\Session\Storage\SessionArrayStorage;
use Laminas\Session\Validator\RemoteAddr;
use Laminas\Session\Validator\HttpUserAgent;
use Doctrine\DBAL\Driver\PDO\MySQL\Driver as PDOMySqlDriver;

return [
    'application' => [
        'defaultLanguage' => 'en_US',
        'company' => []
    ],

    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => PDOMySqlDriver::class,
                'params' => [
                    'host' => '127.0.0.1',
                    'port' => '8889',
                    'user' => 'root',
                    'password' => 'root',
                    'dbname' => 'globaltickets',
                    'encoding' => 'utf8',
                    'driverOptions' => [
                        1002 => 'SET NAMES utf8'
                    ]
                ]
            ]
        ],

        'migrations_configuration' => [
            'orm_default' => [

                'table_storage' => [
                    'table_name' => 'Migrations',
                    'version_column_name' => 'version',
                    'version_column_length' => 1024,
                    'executed_at_column_name' => 'executedAt',
                    'execution_time_column_name' => 'executionTime',
                ],
                'migrations_paths' => [
                    'Migrations' => 'data/Migrations'
                ], // an array of namespace => path

                'migrations' => [], // an array of fully qualified migrations
                'all_or_nothing' => false,
                'check_database_platform' => true,
                'organize_migrations' => 'year_and_month', // year or year_and_month
                'custom_template' => null,
            ],

            'orm_other' => [

            ]
        ],

        'entitymanager' => [
            'orm_default' => [
                'connection' => 'orm_default',
                'configuration' => 'orm_default'
            ]
        ],
        'configuration' => [
            'orm_default' => [
                'proxy_dir' => 'data/DoctrineORMModule/Proxy',
                'proxy_namespace' => 'DoctrineORMModule\Proxy'
            ]
        ],
        'eventmanager' => [
            'orm_default' => []
        ],
        'entity_resolver' => [
            'orm_default' => []
        ],
        'sql_logger_collector' => [
            'orm_default' => []
        ],
        'doctrine_factories' => [
            'authenticationadapter' => 'Auth\Adapter\AuthAdapterFactory'
        ]
    ],
    'company' => [],

    // Session configuration.
    'session_config' => [
        'cookie_lifetime'     => 60 * 60 * 1, // Session cookie will expire in 1 hour.
        'gc_maxlifetime'      => 60 * 60 * 24 * 30, // How long to store session data on server (for 1 month).
    ],
    // Session manager configuration.
    'session_manager' => [
        // Session validators (used for security).
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ]
    ],
    // Session storage configuration.
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
    // Cache configuration.

    'caches' => [
        'FilesystemCache' => [
            'adapter' => \Laminas\Cache\Storage\Adapter\Filesystem::class,

            'options' => [
                // Store cached data in this directory.
                'cache_dir' => './data/cache',
                // Store cached data for 1 hour.
                'ttl' => 60 * 60 * 1
            ],

            'plugins' => [
                [
                    'name' => 'serializer',
                    'options' => [
                    ],
                ],
            ],
        ],
    ],


];

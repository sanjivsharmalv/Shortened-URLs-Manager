<?php

namespace Application\Controller\Factory;

use Application\Service\UserService;
use Application\Service\AuthService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Controller\UserController;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class UserControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $userService = $container->get(UserService::class);
        $authService = $container->get(AuthService::class);
        // Instantiate the controller and inject dependencies
        return new UserController($entityManager, $userService,$authService);
    }
}

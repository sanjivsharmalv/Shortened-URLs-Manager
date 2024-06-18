<?php

namespace Application\Controller\Factory;

use Application\Service\UserService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Controller\AuthController;
use Application\Service\AuthService;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $authService = $container->get(AuthService::class);
        $userService = $container->get(UserService::class);

        return new AuthController($entityManager, $authService, $userService);
    }
}

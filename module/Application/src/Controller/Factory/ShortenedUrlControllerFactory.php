<?php

namespace Application\Controller\Factory;

use Application\Controller\ShortenedUrlController;
use Application\Service\AuthService;
use Application\Service\ShortenedUrlService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class ShortenedUrlControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $shortenedUrlService = $container->get(ShortenedUrlService::class);
        $authService = $container->get(AuthService::class);

        // Instantiate the controller and inject dependencies
        return new ShortenedUrlController($entityManager, $shortenedUrlService, $authService);
    }
}

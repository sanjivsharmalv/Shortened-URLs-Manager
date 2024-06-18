<?php

namespace Application\Service\Factory;

use Application\Service\AuthAdapter;
use Application\Service\AuthService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;
use Laminas\Authentication\Storage\Session as SessionStorage;
use Laminas\Session\Container;

/**
 * This is the factory for PostManager. Its purpose is to instantiate the
 * service.
 */
class AuthServiceFactory implements FactoryInterface
{
    /**
     * This method creates the AuthManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Create a session storage object with the namespace 'Laminas_Auth'
        $storage = new SessionStorage('Laminas_Auth');

        // Instantiate dependencies.
        $laminasAuthService = new \Laminas\Authentication\AuthenticationService($storage);

        $sessionManager = $container->get(SessionManager::class);
        $authAdaptor = $container->get(AuthAdapter::class);

        // Instantiate the AuthManager service and inject dependencies to its constructor.
        return new AuthService($laminasAuthService, $authAdaptor,$sessionManager);
    }
}
<?php

declare(strict_types=1);

namespace Application;

use Application\Entity\User;
use Doctrine\ORM\EntityManager;
use Laminas\Mvc\MvcEvent;
use Application\Listener\CreateAdminUserListener;

class Module
{
    public function getConfig(): array
    {
        /** @var array $config */
        $config = include __DIR__ . '/../config/module.config.php';
        return $config;
    }
    public function onBootstrap(MvcEvent $event)
    {
        $application = $event->getApplication();
        $serviceManager = $application->getServiceManager();
        $entityManager = $serviceManager->get(EntityManager::class);

        $userRepository = $entityManager->getRepository(User::class);
        $existingAdmin = $userRepository->findOneBy(['userName' => 'admin']);

        if (! $existingAdmin) {
            // Create admin user when the app is loaded for the first time
            $adminUser = new User();

            $adminUser->setFirstName('Test First Name');
            $adminUser->setMiddleName('Test M Name');
            $adminUser->setLastName('Test L Name');
            $adminUser->setUserName('admin');
            $adminUser->setEmail('test@globaltickets.nl');
            $adminUser->setState('active');

            // hash the password
            $adminUser->setPassword('Test!234');
            $adminUser->updateModified();

            // Persist and flush
            $entityManager->persist($adminUser);
            $entityManager->flush();
        }
    }
}

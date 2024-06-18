<?php

//CreateAdminUserListener.php - for creating the admin user with default password

namespace Application\Listener;

use Application\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\MvcEvent;

class CreateAdminUserListener extends AbstractListenerAggregate
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, [$this, 'onBootstrap'], $priority);
    }

    public function onBootstrap(MvcEvent $event)
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $existingAdmin = $userRepository->findOneBy(['userName' => 'admin']);

        if (! $existingAdmin) {
            // Create admin user when the app is loaded for the first time
            $adminUser = new User();
            $adminUser->setUserName('admin');
            $adminUser->setEmail('admin@example.com'); // Adjust as per your requirements

            // hash the password
            $hashedPassword = password_hash('Test!234', PASSWORD_DEFAULT);
            $adminUser->setPassword($hashedPassword);

            // Persist and flush
            $this->entityManager->persist($adminUser);
            $this->entityManager->flush();

        }
    }
}

<?php

namespace Application\Service;

use Application\Entity\User;

/**
 * The PostManager service is responsible for adding new posts, updating existing
 * posts, adding tags to post, etc.
 */
class UserService
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager;
     */
    private $entityManager;

    /**
     * Constructor.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function fetchUsers($pageNumber)
    {
        return  $this->entityManager->getRepository(User::class)->fetchUsers($this->entityManager, $pageNumber);
    }
    public function createUser($formData)
    {
        if ($this->doesUserExist($formData['user-name'])) {
            return ['success' => false, 'messageType' => 'User Already Exists.'];
        }

        try {
            $user = new User();

            // Set user information
            $user->setFirstName($formData['user-first-name']);
            $user->setMiddleName($formData['user-middle-name']);
            $user->setLastName($formData['user-last-name']);
            $user->setUserName($formData['user-name']);
            $user->setEmail($formData['user-email']);
            $user->setState('active');

            //User Password
            $userPassword = $formData['user-password'];

            $user->setPassword($userPassword);

            //set created / modified as the case may be
            $user->updateModified();

            $this->entityManager->persist($user);
            // Apply changes to database.
            $this->entityManager->flush();

            return ['success' => true, 'message' => 'User Successfully Registered.'];
        } catch (\Exception $exc) {
            error_log('Error:' . $exc->getMessage() . "\n" . $exc->getTraceAsString());
            return ['success' => false, 'message' => 'An error occurred while registering the user.'];
        }
    }

    /**
     * Checks if a user with the given username already exists.
     *
     * @param string $userName The username to check.
     * @return bool True if the user exists, false otherwise.
     */
    private function doesUserExist(string $userName): bool
    {
        try {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $userName]);
            return $user !== null;
        } catch (\Exception $exc) {
            //print to web server log
            error_log('Error checking if user exists: ' . $exc->getMessage() . "\n" . $exc->getTraceAsString());
            return false;
        }
    }

}

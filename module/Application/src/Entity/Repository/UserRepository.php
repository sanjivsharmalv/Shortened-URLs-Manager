<?php

declare(strict_types=1);

namespace Application\Entity\Repository;

use Application\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    protected const PAGE_SIZE = 2;
    /**
     * @inheritDoc
     */
    public function authenticate(string $credential, ?string $password = null): ?UserInterface
    {
        try {
            /** @var ?User $user */
            $user = $this->findOneBy(['username' => $credential]);

            if ($user === null) {
                return null;
            }

            // If password is null, we're authenticating without password.
            // For example, when using Google or GitHub or Facebook as OAuth2 identity provider
            if ($password === null) {
                return $user;
            }

            /*  @var $user User */
            if (User::hashPassword($user, $password) === true) {
                return $user;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
        }

        return null;
    }

    public function getUserEntityByUserCredentials(string $credential, string $password): ?UserInterface
    {
        $user = $this->findOneBy(['email' => $credential]);
        if (! $user) {
            return null;
        }
        if (! password_verify($password, $user->getPassword())) {
            return null;
        }
        return $user;
    }

    public function fetchUsers($entityManager, $pageNumber)
    {
        // Get today's date, start of the day so that we also include users
        // created /modified today as well
        $today = new \DateTime('today');
        $today->setTime(23, 59, 59);

        // Initialize the query builder for counting total records
        $queryBuilder = $entityManager
            ->getRepository(User::class)
            ->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.modified <= :today')
            ->setParameter('today', $today)
            ->andWhere('u.userName = :userName')
            ->setParameter('userName', 'admin');

        // Get the total number of records
        $totalRecords = $queryBuilder->getQuery()->getSingleScalarResult();

        $users = null;
        $totalPages = 1;

        if ($totalRecords > 0) {
            // Calculate total number of pages
            $totalPages = ceil($totalRecords / self::PAGE_SIZE);
            $offset = ($pageNumber - 1) * self::PAGE_SIZE;

            // Adjust the query builder for fetching user data
            $queryBuilder = $entityManager
                ->getRepository(User::class)
                ->createQueryBuilder('u')
                ->where('u.modified <= :today')
                ->setParameter('today', $today)
                ->andWhere('u.userName != :userName')
                ->setParameter('userName', 'admin')
                ->orderBy('u.modified', 'DESC')
                ->setFirstResult($offset)
                ->setMaxResults(self::PAGE_SIZE);

            // Execute the query and fetch the results
            $users = $queryBuilder->getQuery()->getResult();
        }

        return [$totalPages, $users];
    }

}

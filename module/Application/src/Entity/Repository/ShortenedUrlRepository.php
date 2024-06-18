<?php

declare(strict_types=1);

namespace Application\Entity\Repository;

use Application\Entity\ShortenedUrl;
use Doctrine\ORM\EntityRepository;

class ShortenedUrlRepository extends EntityRepository
{
    private const PAGE_SIZE = 20;
    public function fetchShortenedUrls($entityManager, $user, $pageNumber, $status = 'active')
    {
        // Get today's date, start of the day so that we also include urls
        // created /modified today as well
        $today = new \DateTime('today');
        $today->setTime(23, 59, 59);

        // Initialize the query builder for counting total records
        $queryBuilder = $entityManager
            ->getRepository(ShortenedUrl::class)
            ->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.modified <= :today')
            ->andWhere('u.status = :s')
            ->setParameter('s', $status)
            ->andWhere('u.user = :user')
            ->setParameter('today', $today)
            ->setParameter('user', $user);

        // Get the total number of records
        $totalRecords = $queryBuilder->getQuery()->getSingleScalarResult();

        //$users = null;
        $totalPages = 1;

        if ($totalRecords > 0) {
            // Calculate total number of pages
            $totalPages = ceil($totalRecords / self::PAGE_SIZE);
            $offset = ($pageNumber - 1) * self::PAGE_SIZE;

            // Adjust the query builder for fetching user data
            $queryBuilder = $entityManager
                ->getRepository(ShortenedUrl::class)
                ->createQueryBuilder('u')
                ->where('u.modified <= :today')
                ->andWhere('u.status = :s')
                ->setParameter('s', $status)
                ->setParameter('today', $today)
                ->andWhere('u.user = :user')
                ->setParameter('user', $user)
                ->orderBy('u.modified', 'DESC')
                ->setFirstResult($offset)
                ->setMaxResults(self::PAGE_SIZE);

            // finally fetch the results
            $shortenedUrls = $queryBuilder->getQuery()->getResult();
        }

        return [$totalPages, $shortenedUrls];
    }
}

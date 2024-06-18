<?php

namespace Application\Service;

use Application\Entity\ShortenedUrl;

/**
 * The PostManager service is responsible for adding new posts, updating existing
 * posts, adding tags to post, etc.
 */
class ShortenedUrlService
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

    /*
     * Returns the Shortened Urls
     * @params pageNumber , currentUser - Currently Logged-in User
     * */
    public function fetchShortenedUrls($pageNumber, $currentUser)
    {
        return  $this->entityManager
            ->getRepository(ShortenedUrl::class)
            ->fetchShortenedUrls($this->entityManager, $currentUser, $pageNumber);
    }

    /*
     * Returns the Shortened Urls
     * @params pageNumber , currentUser - Currently Logged-in User
     * */
    public function fetchDeletedShortenedUrls($pageNumber, $currentUser)
    {

        return  $this->entityManager
            ->getRepository(ShortenedUrl::class)
            ->fetchShortenedUrls($this->entityManager, $currentUser, $pageNumber, 'deleted');
    }

    /*
     * Creates an entry for the Shortened URl in the Database
     * Params - $formData - Validated Form Data
     *          $currentUser - Currently logged-in User
     * Returns Result as 'false' if a matching shortened already exists along with the message
     * or if there is another unforeseen reason of not being able to create the URL
     * Returns Success of it is able to create the URL in the system.
     * */
    public function create($formData, $currentUser)
    {
        if ($this->doesShortenedUrlExist($currentUser, $formData['full-url'])) {
            return ['success' => false, 'message' => 'Shortened URL Already Exists.'];
        }

        try {
            $shortenedUrl = new ShortenedUrl();

            // Set user information
            $shortenedUrl->setFullUrl($formData['full-url']);
            $shortenedUrl->setShortenedUrl($formData['shortened-url']);
            $shortenedUrl->setDescription($formData['description']);

            //set initial status
            $shortenedUrl->setStatus('active');
            $shortenedUrl->setComments($formData['comments']);

            $shortenedUrl->setUser($currentUser);

            //set created / modified as the case may be
            $shortenedUrl->updateModified();

            $this->entityManager->persist($shortenedUrl);
            // Apply changes to database.
            $this->entityManager->flush();

            return ['success' => true, 'message' => 'Shortened Url Successfully Created.'];
        } catch (\Exception $exc) {
            error_log('Error:' . $exc->getMessage() . "\n" . $exc->getTraceAsString());
            return ['success' => false, 'message' => 'An error occurred while creating the shortened url.'];
        }
    }

    /*
     * Edits an already existing entry in the database for the Shortened URl
     * Params - $formData - Validated Form Data
     *          $currentUser - Currently logged-in User
     * Returns Result as 'false' if a matching shortened already exists along with the message
     * or if there is another unforeseen reason of not being able to create the URL
     * Returns Success of it is able to create the URL in the system.
     * */
    public function edit($formData, $currentUser, $passedShortenedUrl)
    {
        /*
         * if passed short url id matches with the one in db and
         * they have the same full url that means we wont allow
         * */
        $shortenedUrl = $this->doesShortenedUrlExist($currentUser, $formData['full-url']);
        if ($shortenedUrl->getId() != $passedShortenedUrl->getId()) {
            return ['success' => false, 'message' => 'Shortened URL Already Exists.'];
        }
        try {
            // Set user information
            $shortenedUrl->setFullUrl($formData['full-url']);
            $shortenedUrl->setShortenedUrl($formData['shortened-url']);
            $shortenedUrl->setDescription($formData['description']);

            //$shortenedUrl->setStatus($formData['status-type']);

            $shortenedUrl->setComments($formData['comments']);

            $shortenedUrl->setUser($currentUser);

            //set created / modified as the case may be
            $shortenedUrl->updateModified();

            $this->entityManager->persist($shortenedUrl);
            // Apply changes to database.
            $this->entityManager->flush();

            return ['success' => true, 'message' => 'Shortened Url Successfully Updated.'];
        } catch (\Exception $exc) {
            error_log('Error:' . $exc->getMessage() . "\n" . $exc->getTraceAsString());
            return ['success' => false, 'message' => 'An error occurred while creating the shortened url.'];
        }
    }

    /**
     * Mark delete - Shortened Url
     * Params - ShortenedUrl object
     */
    public function delete($shortenedUrl)
    {
        try {
            $shortenedUrl->setStatus('deleted');
            $this->entityManager->persist($shortenedUrl);
            $this->entityManager->flush();
            return [
                'messageType' => true,
                'message' => 'URL successfully deleted.'
            ];
        } catch (\Exception $ex) { //return graceful message instead of exposing internals
            error_log($ex->getMessage());
            return [
                'messageType' => false,
                'message' => 'An error occurred while trying to delete the URL. Please try again later.'
            ];
        }
    }

    /**
     * Mark restored - Shortened Url
     * Params - ShortenedUrl object
     */
    public function restore($shortenedUrl)
    {
        try {
            $shortenedUrl->setStatus('active');
            $this->entityManager->persist($shortenedUrl);
            $this->entityManager->flush();
            return [
                'messageType' => true,
                'message' => 'URL successfully Restored.'
            ];
        } catch (\Exception $ex) { //return graceful message instead of exposing internals
            error_log($ex->getMessage());
            return [
                'messageType' => false,
                'message' => 'An error occurred while trying to restore the URL. Please try again later.'
            ];
        }
    }

    /**
     * Checks if a user with the given Full Url already exists.
     *
     * @param string $shortenedUrlName The Full Url to check.
     * @return bool True if the Full Url exists, false otherwise.
     */
    private function doesShortenedUrlExist($currentUser, string $fullUrl): mixed
    {
        $pageNumber = 1;
        [$totalRecords,$shortenedUrls ] = $this->entityManager
            ->getRepository(ShortenedUrl::class)
            ->fetchShortenedUrls($this->entityManager, $currentUser, $pageNumber, 'active');

        $isUrlPresent = false;

        foreach ($shortenedUrls as $shortenedUrl) {
            if ($shortenedUrl->getFullUrl() === $fullUrl) {
                return $shortenedUrl;
            }
        }
        return false;
    }
}

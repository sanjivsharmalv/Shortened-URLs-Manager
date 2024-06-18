<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Entity\ShortenedUrl;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Application\Form\ShortenedUrlForm;
use Application\Entity\User;

/*
 * A controller responsible for implementing CRUD operations for shortened URLs
 * typically manages the interactions between the user interface and the data model,
 * allowing users to create, read, update, and delete shortened URLs.
 * Here's a short description of what such a controller might entail:
 * */
class ShortenedUrlController extends AbstractActionController
{
    private $entityManager;
    private $shortenedUrlService;
    private $authService;

    public function __construct($entityManager, $shortenedUrlService, $authService)
    {
        $this->entityManager = $entityManager;
        $this->shortenedUrlService = $shortenedUrlService;
        $this->authService = $authService;
    }

    /*
     * Returns the list of Shortened URLs for the currently logged-in user
     * Params - Takes in page_number,
     * Returns the template for displaying the urls.
     * */

    public function indexAction()
    {
        $vm = new ViewModel();
        $request = $this->getRequest();

        //get Query Parameters
        $queryParameters = $request->getQuery();

        $messageType = $queryParameters->get('messageType', '');
        $vm->setVariable('messageType', $messageType);
        $message = $queryParameters->get('message', '');
        $vm->setVariable('message', $message);

        //get the pageNumber from current user company's settings
        $pageNumber = (int) $this->params()->fromRoute('page_number', 1);
        $pageNumber = is_numeric($pageNumber) && $pageNumber > 0 ? (int) $pageNumber : 1;
        $currentUser = $this->getCurrentUser();

        [$totalPages,$shortenedUrls] = $this->shortenedUrlService->fetchShortenedUrls($pageNumber, $currentUser);
        $vm->setVariable('user', $currentUser);
        $vm->setVariable('totalPages', $totalPages);
        $vm->setVariable('shortenedUrls', $shortenedUrls);
        $vm->setTemplate('./application/shortenedUrl/shortenedUrls');
        return $vm;
    }

    /*
     * Create: Accepts a long URL input, generates a unique shortened URL,
     * and stores it in the database.
     * */
    public function createAction()
    {
        $messageType = null;

        $vm = new ViewModel();
        $request = $this->getRequest();

        $scenario = 'create';
        $vm->setVariable('scenario', $scenario);

        // Create user.
        $currentUser = $this->getCurrentUser();

        // Create the form
        $shortenedUrlForm = new ShortenedUrlForm($scenario);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            // Fill in the form with POST data
            $data = $this->params()->fromPost();
            $shortenedUrlForm->setData($data);

            // Validate form
            if ($shortenedUrlForm->isValid()) {
                // Get filtered and validated data
                $data = $shortenedUrlForm->getData();

                $result = $this->shortenedUrlService->create($data, $currentUser);

                if ($result['success']) {
                    $messageType = 'success';
                    $message = $result['message'];
                    $vm->setVariable('message', $message);
                    return $this->redirect()
                        ->toUrl(url: '/shortenedurls?messageType=' . $messageType . '&message=' . $message);
                } else {// if full Url already exists
                    $messageType = 'error';
                    $message = $result['message'];
                    $vm->setVariable('message', $message);
                    $vm->setVariable('formData', $data);
                }
            } else {
                $messageType = "error";
                $messages = $shortenedUrlForm->getMessages();
                $vm->setVariable('messages', $messages);
            }
        }
        $vm->setVariable('user', $currentUser);
        $vm->setVariable('shortenedUrlForm', $shortenedUrlForm);
        $vm->setVariable('messageType', $messageType);
        $vm->setTemplate('./application/shortenedurl/create');
        return $vm;
    }

    /*
     * Update: Modifies existing shortened URLs, such as updating the associated
     * long URL or custom alias.
     * Params = id of the given Shortened URl
     * */
    public function editAction()
    {
        $messageType = null;
        $message = null;

        $vm = new ViewModel();

        $urlId = $this->params()->fromRoute('id', -1);
        // Get Create user.
        $currentUser = $this->getCurrentUser();
        //validate and fetch Url obj
        $shortenedUrl = $this->validateUrl($urlId, $currentUser);

        //if id is invalid go to the list of urls
        if (is_null($shortenedUrl)) {
            $messageType = 'error';
            $message = 'Shortened URL Obj do not exist or id is invalid';

            return $this->redirect()
                ->toUrl(url: '/shortenedurls?messageType=' . $messageType . '&message=' . $message);
        }
        $vm->setVariable('shortenedUrl', $shortenedUrl);

        $scenario = 'edit';
        $vm->setVariable('scenario', $scenario);

        // Create the form
        $shortenedUrlForm = new ShortenedUrlForm($scenario,$this->entityManager,$shortenedUrl);
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();
            $shortenedUrlForm->setData($data);

            // Validate form
            if ($shortenedUrlForm->isValid()) {
                
                // Get filtered and validated data
                $data = $shortenedUrlForm->getData();

                $result = $this->shortenedUrlService->edit($data, $currentUser, $shortenedUrl);

                if ($result['success']) {
                    
                    $messageType = 'success';
                    $message = $result['message'];
                    $vm->setVariable('message', $message);
                   /* return $this->redirect()
                        ->toUrl(url: '/shortenedurls?messageType=' . $messageType . '&message=' . $message);*/
                } else {// if full Url already exists

                    $messageType = 'error';

                    $message = $result['message'];

                    $vm->setVariable('message', $message);
                    $vm->setVariable('formData', $data);
                }
            } else {
                
                $messageType = "error";
                $messages = $shortenedUrlForm->getMessages();
                $vm->setVariable('messages', $messages);
            }
        }
        
        $vm->setVariable('user', $this->getCurrentUser());
        $vm->setVariable('shortenedUrlForm', $shortenedUrlForm);
        
        $vm->setVariable('messageType', $messageType);
        $vm->setTemplate('./application/shortenedurl/edit');
        return $vm;
    }

    /*
    *   Delete: Deletes the Shortened Link. Please note that it just mark the shortened url
     *  as 'delete'.
     *  Params - id of the url to be deleted.
     *
    **/
    public function deleteAction()
    {
        // Get the currently logged-in user
        $currentUser = $this->getCurrentUser();

        // Get the request object and URL ID from the route parameters
        $urlId = $this->params()->fromRoute('id', null);

        // Get the request object and URL ID from the route parameters
        $mode = $this->params()->fromRoute('mode', 'showdialog');

        // If URL ID is null, throw a 404 exception
        if (is_null($urlId)) {
            return $this->notFound();
        }

        $shortenedUrl = $this->validateUrl($urlId, $currentUser);

        // If the shortened URL does not exist, throw a 404 exception
        if (! $shortenedUrl) {
            return $this->notFound();
        }

        if ($mode == 'confirmdelete') {
            // Attempt to delete the shortened URL
            $result = $this->shortenedUrlService->delete($shortenedUrl);
            $message = $result['message'];
            $messageType = $result['messageType'];
            return $this->redirect()->toUrl('/shortenedurls?messageType=' . $messageType . '&message=' . urlencode($message));
        }

        $vm = new ViewModel();
        $vm->setVariable('user', $currentUser);
        $vm->setVariable('shortenedUrl', $shortenedUrl);
        $vm->setTemplate('./application/shortenedurl/delete');
        return $vm;
    }

    /*
     * The function The application enables redirection from the
     * shortened URL to the specified target URL.
     * Param - id , of the url to be redirected.
     * */
    public function redirectAction()
    {
        // Get the currently logged-in user
        $currentUser = $this->getCurrentUser();

        // Get the request object and URL ID from the route parameters
        $urlId = $this->params()->fromRoute('id', null);

        // If URL ID is null, throw a 404 exception
        if (is_null($urlId)) {
            return $this->notFound();
        }

        $shortenedUrl = $this->validateUrl($urlId, $currentUser);

        if (! $shortenedUrl || $shortenedUrl->getStatus() === 'deleted') {
            return $this->notFound();
        }

        return $this->redirect()->toUrl($shortenedUrl->getFullUrl());
    }

    /*
     * The function returns the view for the selected Shortened Url
     * Param - id , of the url to be redirected.
     * */
    public function viewAction()
    {
        // Get the currently logged-in user
        $currentUser = $this->getCurrentUser();

        // Get the request object and URL ID from the route parameters
        $urlId = $this->params()->fromRoute('id', null);

        // If URL ID is null, throw a 404 exception
        if (is_null($urlId)) {
            return $this->notFound();
        }

        $shortenedUrl = $this->validateUrl($urlId, $currentUser);

        if (! $shortenedUrl || ($shortenedUrl && $shortenedUrl->getStatus() === 'deleted')) {
            return $this->notFound();
        }

        $vm = new ViewModel();
        $vm->setVariable('user', $currentUser);
        $vm->setVariable('shortenedUrl', $shortenedUrl);
        $vm->setTemplate('./application/shortenedurl/view');
        return $vm;
    }

    /* Validates and returns if the passed url belongs to the logged in user
     * */
    private function validateUrl($urlId, $currentUser)
    {
        // Retrieve the shortened URL entity
        $shortenedUrl = $this->entityManager->getRepository(ShortenedUrl::class)->find($urlId);

        if (! $shortenedUrl || $shortenedUrl->getStatus() === 'deleted') {
            return $shortenedUrl;
        }

        $userName = $shortenedUrl->getUser()->getUserName();

        //check if passed in url id belongs to the currently logged in user
        if (! ($currentUser->getUserName() == $userName)) {
            $shortenedUrl = null;
        }

        return $shortenedUrl;
    }

    private function getCurrentUser()
    {
        // Check if user is logged in.
        if ($this->authService->hasIdentity()) {
            // Fetch User entity from database.
            $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($this->authService->getIdentity());
            if ($user == null) {
                // Oops.. the identity presents in session, but there is no such user in database.
                // We throw an exception, because this is a possible security problem.
                return $this->notFound();
            }
            // Return found User.
            return $user;
        }
        return null;
    }

    private function notFound()
    {
        $vm = new ViewModel();
        $vm->setTemplate('./application/shortenedurl/resource_not_found');
        return $vm;
    }


    /*
     * Returns the list of deleted Shortened URLs for the currently logged-in user
     * Params - Takes in page_number,
     * Returns the template for displaying the urls.
     * */

    public function listTrashAction()
    {
        $vm = new ViewModel();
        $request = $this->getRequest();

        //get Query Parameters
        $queryParameters = $request->getQuery();

        $messageType = $queryParameters->get('messageType', '');
        $vm->setVariable('messageType', $messageType);
        $message = $queryParameters->get('message', '');
        $vm->setVariable('message', $message);

        //get the pageNumber from current user company's settings
        $pageNumber = (int) $this->params()->fromRoute('page_number', 1);
        $pageNumber = is_numeric($pageNumber) && $pageNumber > 0 ? (int) $pageNumber : 1;
        $currentUser = $this->getCurrentUser();

        [$totalPages,$shortenedUrls] = $this->shortenedUrlService->fetchDeletedShortenedUrls($pageNumber, $currentUser);
        $vm->setVariable('user', $currentUser);
        $vm->setVariable('totalPages', $totalPages);
        $vm->setVariable('shortenedUrls', $shortenedUrls);
        $vm->setTemplate('./application/shortenedUrl/list_trash');
        return $vm;
    }

    /*
    *   Restore: Restores the Shortened Link. Please note that it undo the delete status
     *  Params - id of the url to be deleted.
     *
    **/
    public function restoreAction()
    {
        // Get the currently logged-in user
        $currentUser = $this->getCurrentUser();

        // Get the request object and URL ID from the route parameters
        $urlId = $this->params()->fromRoute('id', null);

        // If URL ID is null, throw a 404 exception
        if (is_null($urlId)) {
            return $this->notFound();
        }

        $shortenedUrl = $this->validateUrl($urlId, $currentUser);

        // If the shortened URL does not exist, throw a 404 exception
        if (! $shortenedUrl) {
            return $this->notFound();
        }

        $result = $this->shortenedUrlService->restore($shortenedUrl);
        $message = $result['message'];
        $messageType = $result['messageType'];
        return $this->redirect()->toUrl('/shortenedurls?messageType=' . $messageType . '&message=' . urlencode($message));
    }
}

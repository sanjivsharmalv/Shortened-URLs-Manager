<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Entity\User;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Application\Form\UserForm;

/*
 * Controller for registering the user, the plan was to implement all CRUD
 * operations for User but in the interest of time I chose to implement the bare minimum functionality.
*/
class UserController extends AbstractActionController
{
    private $entityManager;
    private $userService;
    private $authService;

    public function __construct($entityManager, $userService,$authService)
    {
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->authService = $authService;
    }
    /*
    *
    * Displays the list of users.
    *
    * @param {number} [pageNumber] - Optional. The page number to display.
    * @returns {void}
    * @description The function fetches and displays a list of users. Pagination functionality
    * is implemented but currently incomplete.
    */
    public function indexAction()
    {
        $vm = new ViewModel();
        $request = $this->getRequest();

        //get Query Parameters
        $queryParameters = $request->getQuery();

        // Create user.
        $currentUser = $this->getCurrentUser();

        $userMessageType = $queryParameters->get('userMessageType', '');
        $vm->setVariable('userMessageType', $userMessageType);
        $userMessage = $queryParameters->get('userMessage', '');
        $vm->setVariable('userMessage', $userMessage);

        //get the locale from current user company's settings
        $pageNumber = $this->params()->fromRoute('page_number', null);
        $pageNumber = is_numeric($pageNumber) && $pageNumber > 0 ? (int) $pageNumber : 1;
        [$totalPages,$users] = $this->userService->fetchUsers($pageNumber);
        $vm->setVariable('totalPages', $totalPages);
        $vm->setVariable('users', $users);

        $vm->setTemplate('./application/user/users');
        return $vm;
    }

    /**
     * Handles user creation form submission and processing.
     *
     * @return Laminas\View\Model\ViewModel
     */
    public function createAction()
    {
        $userMessageType = null;

        $vm = new ViewModel();

        $scenario = 'create';
        $vm->setVariable('scenario', $scenario);

        // Create the form
        $userForm = new UserForm($scenario);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            // Fill in the form with POST data
            $data = $this->params()->fromPost();
            $userForm->setData($data);

            // Validate form
            if ($userForm->isValid()) {
                // Get filtered and validated data
                $data = $userForm->getData();

                // Create user.
                $result = $this->userService->createUser($data);

                if ($result['success']) {
                    $userMessageType = 'success';
                    $userMessage = $result['message'];
                    $vm->setVariable('userMessage', $userMessage);
                    return $this->redirect()
                        ->toUrl(url: '/home?messageType=' . $userMessageType . '&message=' . $userMessage);
                } else {// if user already exists
                    $userMessageType = 'error';
                    $userMessage = $result['message'];
                    $vm->setVariable('userMessage', $userMessage);
                    $vm->setVariable('formData', $data);
                }
            } else {
                $userMessageType = "error";
                $userErrorMessages = $userForm->getMessages();
                $vm->setVariable('userErrorMessages', $userErrorMessages);
            }
        }
        $vm->setVariable('userForm', $userForm);
        $vm->setVariable('userMessageType', $userMessageType);
        $vm->setTemplate('./application/user/create');
        return $vm;
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

}

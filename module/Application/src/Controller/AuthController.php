<?php

namespace Application\Controller;

use Laminas\I18n\Translator\Translator;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Authentication\Result;
use Laminas\Uri\Uri;
use Application\Form\LoginForm;
use Application\Entity\User;

/**
 * This controller is responsible for letting the user to log in and log out.
 */
class AuthController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Auth manager.
     * @var Application\Service\authService
     */
    private $authService;

    /**
     * User manager.
     * @var Application\Service\userService
     */
    private $userService;

    /**
     * Constructor.
     */
    public function __construct($entityManager, $authService, $userService)
    {
        $this->entityManager = $entityManager;
        $this->authService  = $authService ;
        $this->userService = $userService ;
    }

    /**
     * The loginAction function orchestrates the login process for users in an application,
     * handling form submission, authentication checks, and redirection based on user roles.
     * It logs actions for debugging purposes, sets up a ViewModel to pass data to the view layer,
     * validates form inputs, and manages login attempts.
     * it ensures a seamless user experience by redirecting authenticated users appropriately
     * and displaying error messages for failed login attempts.
     */
    public function loginAction()
    {
        error_log('Login Action');
        $vm = new ViewModel();
        $user = null;

        // Create login form
        $form = new LoginForm($this->entityManager);
        $form->get('redirect_url')->setValue((string) $this->params()->fromQuery('redirectUrl', ''));

        if ($this->getRequest()->isPost()) {
            error_log('In Post');
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $alreadyLoggedIn = $this->authService->alreadyLoggedIn();

                $user = $this->entityManager->getRepository(User::class)->findOneByEmail($data['email']);
                $homeUrl = $this->getHomeUrl($user);//get the url depending on the role - admin/normal

                if ($alreadyLoggedIn) {
                    error_log('user already logged in');
                    $message = "User is Already Logged in.";
                    $messageType = 'alreadyloggedin';
                    return $this->redirect()
                        ->toUrl(url: $homeUrl . '/?messageType=' . $messageType . '&message=' . $message);
                } else {    //login
                    $result = $this->authService->login($data['email'], $data['password']);
                    if ($result->getCode() === Result::SUCCESS) {
                        $homeUrl = $this->getHomeUrl($user);//get the url depending on the role - admin/normal
                        $this->redirect()->toUrl($homeUrl);
                    } else {
                        $isLoginError = true;
                        $vm->setTemplate('./application/auth/login_error.phtml');
                        return $vm;
                    }
                }
            } else {
                $loginErrorMessages = $form->getMessages();
                $vm->setVariable('loginErrorMessages', $loginErrorMessages);
                var_dump($loginErrorMessages);
                $parsedErrors = $this->parseValidationErrors($loginErrorMessages);
                $vm->setTemplate('./application/auth/login.phtml');
                // Handle form validation errors
                $vm->setVariable('loginErrorMessages', $parsedErrors);
            }
        }

        // Set variables for the view
        $vm->setVariable('user', $user);
        $vm->setVariable('loginForm', $form);
        $vm->setVariable('isLoginError', isset($isLoginError) ? $isLoginError : false);
        $vm->setVariable('loginMessageType', isset($loginMessageType) ? $loginMessageType : '');
        $vm->setVariable('redirectUrl', $this->params()->fromQuery('redirectUrl', ''));

        $vm->setTemplate('./application/auth/login.phtml');
        return $vm;
    }

    private function getHomeUrl($user)
    {

        if ($user->getUserName() == 'admin') {
            return('/users');
        } else {
            return('/shortenedurls');
        }
    }
    private function parseValidationErrors(array $errors)
    {
        $parsedErrors = [];

        foreach ($errors as $field => $fieldErrors) {
            foreach ($fieldErrors as $errorType => $errorMessage) {
                var_dump('errorType:');
                var_dump($errorType);
                if ($errorType == 'regexNotMatch') {
                    $errorMessage = 'Invalid email format. Please use a format like example@domain.com';
                }
                $parsedErrors[] = [
                    'field' => $field,
                    'message' => $errorMessage,
                ];
            }
        }

        return $parsedErrors;
    }
    /**
     * The "logout" action performs logout operation.
     */
    public function logoutAction()
    {
        $vm = new ViewModel();
        $result = $this->authService->logout();
        if ($result['success']) {
            $this->redirect()->toUrl('/home');
        } else { //failure
            $template = './application/shortenedurl/resource_not_found.phtml';
        }
        $vm->setTemplate($template);
        return $vm;
    }

    /**
     * Displays the "Not Authorized" page.
     */
    public function notAuthorizedAction()
    {
        $request = $this->getRequest();
        $url = $request->getUriString();
        echo $url;

        $this->getResponse()->setStatusCode(403);

        return new ViewModel();
    }
}

<?php

namespace Application\Service;

use Application\Service\AuthAdapter;
use Laminas\Authentication\Result;
use Laminas\Authentication\AuthenticationService;

/**
 * The AuthManager service is responsible for user's login/logout and simple access
 * filtering. The access filtering feature checks whether the current visitor
 * is allowed to see the given page or not.
 */
class AuthService
{
    // Constants returned by the access filter.
    private const ACCESS_GRANTED = 1; // Access to the page is granted.
    private const AUTH_REQUIRED  = 2; // Authentication is required to see the page.
    private const ACCESS_DENIED  = 3; // Access to the page is denied.

    /**
     * Authentication service.
     * @var AuthenticationService
     */
    private $laminasAuthService;

    /**
     * Session manager.
     * @var Laminas\Session\SessionManager
     */
    private $sessionManager;

    private $authAdapter;

    /**
     * Constructs the service.
     */
    public function __construct($laminasAuthService, $authAdapter, $sessionManager)
    {
        $this->authAdapter = $authAdapter;
        $this->laminasAuthService = $laminasAuthService;
        $this->sessionManager = $sessionManager;
    }

    /**
     * Check if the user is Already Logged In
     * Returns Boolean
     */
    public function alreadyLoggedIn(): bool
    {

        $alreadyloggedIn = false;
        // Check if user has already logged in. If so, do not allow to log in twice.
        if ($this->laminasAuthService->getIdentity() != null) {
            $alreadyloggedIn = true;
        }

        return $alreadyloggedIn;
    }

    /**
     * Performs a login attempt. If $rememberMe argument is true, it forces the session
     * to last for one month (otherwise the session expires on one hour).
     */
    public function login($email, $password)
    {
        try {
            $this->authAdapter->setEmail($email);
            $this->authAdapter->setPassword($password);
            $result = $this->laminasAuthService->authenticate($this->authAdapter);
            return $result;
        } catch (\Exception $e) {
            // Log the error
            error_log('Error during authentication: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            // Return a failed authentication result
            $failedResult = new \Laminas\Authentication\Result(
                \Laminas\Authentication\Result::FAILURE,
                null,
                ['An error occurred during authentication.']
            );

            return $failedResult;
        }
    }

    /**
     * Performs user logout.
     */
    /*public function logout()
    {
        $userLoggedIn = true;
        // Remove identity from session.
        $this->laminasAuthService->clearIdentity();
        $this->sessionManager->forgetMe();
        $this->sessionManager->destroy();

        try {
            // Allow to log out only when the user is logged in.
            if ($this->laminasAuthService->getIdentity() == null) {
                $userLoggedIn = false;
                return ! $userLoggedIn;
            }
            return ! $userLoggedIn;
        } catch (\Exception $exc) {
            // Log the error
            error_log('Error during logout: ' . $exc->getMessage() . "\n" . $exc->getTraceAsString());
            throw $exc;
        }
    }*/
    public function logout()
    {
        // Log the action for debugging or tracking purposes
        error_log('Logout action initiated.');

        // Remove identity from session
        $this->laminasAuthService->clearIdentity();
        $this->sessionManager->forgetMe();
        $this->sessionManager->destroy();

        try {
            // Check if the user is successfully logged out
            if ($this->laminasAuthService->getIdentity() == null) {
                $message = 'You have been successfully logged out.';
                return ['success' => true, 'message' => $message];
            } else {
                $message = 'Logout failed. User is still logged in.';
                return ['success' => false, 'message' => $message];
            }
        } catch (\Exception $exc) {
            // Log the error
            error_log('Error during logout: ' . $exc->getMessage() . "\n" . $exc->getTraceAsString());
            throw $exc;
        }
    }
    /*
     * This function checks if a user is currently authenticated
     * and returns true if they are, false if they are not,
     * and null if an error occurs during the check.
     *
     * */
    public function hasIdentity()
    {
        try {
            if ($this->laminasAuthService->hasIdentity()) {
                return true;
            } else {
                echo "No user is currently authenticated.";
                return false;
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            echo "An error occurred while checking the identity: " . $e->getMessage();
            return null;
        }
    }

    /*
     * returns the authenticated user's identity if they are logged in,
     * -outputs a message if no user is authenticated, and
     * -handles exceptions by displaying an error message and returning null.
     * */
    public function getIdentity()
    {
        try {
            if ($this->laminasAuthService->hasIdentity()) {
                $identity = $this->laminasAuthService->getIdentity();
                return $identity;
            } else {
                echo "No user is currently authenticated.";
                return null;
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            echo "An error occurred while checking the identity: " . $e->getMessage();
            return null;
        }
    }
}

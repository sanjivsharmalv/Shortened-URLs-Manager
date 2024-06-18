<?php

namespace Application\Form;

use Application\Validator\CheckPasswordAccountValidator;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use User\Validator\LoginValidator;
use Application\Validator\CheckAccountValidator;

/**
 * This form is used to collect user's login, password and 'Remember Me' flag.
 */
class LoginForm extends Form implements InputFilterProviderInterface
{
    protected $entityManager = null;
    /**
     * Constructor.
     */
    public function __construct($entityManager)
    {
        // Define form name
        parent::__construct('login-form');

        // Set POST method for this form
        $this->setAttribute('method', 'POST');

        $this->entityManager = $entityManager;
        $this->setAttribute('novalidate', true);
        $this->addElements();
        //$this->addInputFilter();
    }

    public function getInputFilterSpecification(): array
    {
        return [
            [
                'name'     => 'email',
                'required'    => false,
                'allow_empty' => true,
            ],
            [
                'name'     => 'password',
                'required'    => false,
                'allow_empty' => true,
            ],
        ];
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {
        // Add "email" field
        $this->add([
            'type'  => 'email',
            'name' => 'email',
            'attributes' => [
                'id' => 'email',
                'class' => "form-control input-text",
                'placeholder' => "Enter Email",
                'required' => false
            ],
            'options' => [
                'label' => 'E-mail',
            ],
        ]);

        // Add "password" field
        $this->add([
            'type'  => 'password',
            'name' => 'password',
            'attributes' => [
                'id' => 'email',
                'class' => "form-control input-text",
                'required' => true
            ],
            'options' => [
                'label' => 'Password',
            ],
        ]);

        // Add "redirect_url" field
        $this->add([
            'type'  => 'hidden',
            'name' => 'redirect_url'
        ]);

        // Add the CSRF field
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                'timeout' => 600
                ]
            ],
        ]);

        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'login-submit',
            'attributes' => [
                'value' => 'Sign in',
                'id' => 'login-submit',
            ],
        ]);
    }
}

<?php

namespace Application\Form;

use Application\Validator\NameValidator;
use Application\Validator\PasswordMatchValidator;
use Application\Validator\PasswordValidator;
use Laminas\Form\Form;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilter;
use User\Validator\UserExistsValidator;
use User\Validator\UserValidator;
use Laminas\Form\Element\Text;
use Laminas\Form\View\Helper\FormInput;
use Laminas\Form\Element;
use Laminas\InputFilter\Input;
use Laminas\Captcha\Figlet;

/**
 * This form is used to collect user's email, full name, password and status. The form
 * can work in two scenarios - 'create' and 'update'. In 'create' scenario, user
 * enters password, in 'update' scenario s/he doesn't enter password.
 */
class UserForm extends Form
{
    /**
     * Scenario ('create' or 'update').
     * @var string
     */
    private $scenario;

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager = null;

    /**
     * Current user.
     * @var User\Entity\User
     */
    private $user = null;

    /**
     * Constructor.
     */
    public function __construct($scenario = 'create', $entityManager = null, $user = null)
    {
        // Define form name
        parent::__construct('user-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->setAttribute('novalidate', 'true');

        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;
        $this->user = $user;
        $this->setAttribute('class', 'grid grid-cols-1 gap-2');
        $this->setAttribute('novalidate', true);
        $this->setAttribute('action', '/users/create');

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {
        // Add "first_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'user-first-name',
            'attributes' => [
                'id' => 'user-first-name',
                'class' => "input input-bordered w-full",
                'required' => true
            ],
        ]);

        // Add "middle_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'user-middle-name',
            'attributes' => [
                'id' => 'user-middle-name',
                'class' => "input input-bordered w-full",
                'required' => true
            ],
        ]);

        // Add "last_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'user-last-name',
            'attributes' => [
                'id' => 'user-last-name',
                'class' => "input input-bordered w-full",
                'required' => true
            ],
        ]);

        // Add "user-name" field
        $this->add([
            'type'  => 'text',
            'name' => 'user-name',
            'attributes' => [
                'id' => 'user-name',
                'class' => "input input-bordered w-full",
                'required' => true
            ],
        ]);

        // Add "email" field
        $this->add([
            'type'  => 'text',
            'name' => 'user-email',
            'attributes' => [
                'id' => 'user-email',
                'class' => "input input-bordered w-full",
                'placeholder' => "name@example.com",
                'required' => true
            ],
        ]);

        if ($this->scenario == 'create') {
            // Add "password" field
            $this->add([
                'type'  => 'password',
                'name' => 'user-password',
                'attributes' => [
                    'id' => 'user-password',
                    'class' => "form-control input-text",
                    'placeholder' => "Password",
                    'required' => true
                ],
            ]);

            // Add "user-confirm-password" field
            $this->add([
                'type'  => 'password',
                'name' => 'user-confirm-password',
                'attributes' => [
                    'id' => 'user-confirm-password',
                    'class' => "form-control input-text",
                    'placeholder' => "Confirm Password",
                    'required' => true
                ],
            ]);
        }

        // Add the CSRF field
        $this->add([
            'type' => 'csrf',
            'name' => 'user_csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ],
        ]);

        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'user-submit',
            'attributes' => [
                'id' => 'user-submit',
                'required' => true,
                'value' => 'Register',
            ],
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter()
    {
        // Create main input filter
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'name' => 'user-email',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'allow' => \Laminas\Validator\Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'user-first-name',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => NameValidator::class,
                    'options' => [
                        'type' => 'user-first-name',
                        'max' => 50, // Maximum length for username
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'user-middle-name',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => NameValidator::class,
                    'options' => [
                        'type' => 'user-middle-name',
                        'max' => 50, // Maximum length for username
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'user-last-name',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => NameValidator::class,
                    'options' => [
                        'type' => 'user-last-name',
                        'max' => 50, // Maximum length for username
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'user-name',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => NameValidator::class,
                    'options' => [
                        'type' => 'user-name',
                        'max' => 50, // Maximum length for username
                        'entityManager' => $this->entityManager,
                        'itemType' => 'Application\Entity\User',
                    ],
                ],
            ],
        ]);

        if ($this->scenario == 'create') {
            $inputFilter->add([
                'name'     => 'user-password',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => PasswordValidator::class,
                        'options' => [
                            'type' => 'user-password',
                            'max' => 50, // Maximum length for username
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name'     => 'user-confirm-password',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => PasswordMatchValidator::class,
                        'options' => [
                            'token' => 'password', // Field to compare with ('password' field)
                        ],
                    ],
                ],
            ]);
        }
    }
}

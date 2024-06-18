<?php

namespace Application\Form;

use Application\Validator\StringValidator;
use Laminas\Validator;
use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\Validator\Uri as UriValidator;
use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * This form is used to collect the data about the Shortened Url.
 */
class ShortenedUrlForm extends Form implements InputFilterProviderInterface
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
    private $statusTypes;

    /**
     * .
     * @var Application\Entity\ShortenedUrl
     */
    private $shortenedUrl = null;

    /**
     * Constructor.
     */
    public function __construct($scenario = 'create', $entityManager = null, $shortenedUrl = null)
    {
        // Define form name
        parent::__construct('shortenedUrl-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->setAttribute('novalidate', 'true');

        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;
        $this->shortenedUrl = $shortenedUrl;
        $this->setAttribute('class', 'grid grid-cols-1 gap-2');
        $this->setAttribute('novalidate', true);

        if($scenario == 'edit')
            $this->setAttribute('action', '/shortenedurls/' . $scenario.'/'.$shortenedUrl->getId());
        else
            $this->setAttribute('action', '/shortenedurls/' . $scenario);


        $this->statusTypes = ['active' => 'Active','deleted' => 'Deleted'];

        $this->addElements();
        $this->addInputFilter();
    }

    public function getInputFilterSpecification()
    {
        return [
            'status-type' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
                'validators' => [
                    // Add validators here if needed
                ],
            ],
            'full-url' => [
                'required' => false,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\Uri::class,
                        'options' => [
                            'allowRelative' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {
        // Add "shortened-url" field
        $this->add([
            'type'  => 'text',
            'name' => 'full-url',
            'attributes' => [
                'id' => 'full-url',
                'class' => "input input-bordered w-full",
                'required' => false
            ],
        ]);
        // Add "shortened-url" field
        $this->add([
            'type'  => 'text',
            'name' => 'shortened-url',
            'attributes' => [
                'id' => 'shortened-url',
                'class' => "input input-bordered w-full",
                'required' => true
            ],
        ]);

        // Add "description" field
        $this->add([
            'type'  => 'text',
            'name' => 'description',
            'attributes' => [
                'id' => 'description',
                'class' => "input input-bordered w-full",
                'required' => true
            ],
        ]);

        // Add "comments" field
        $this->add([
            'type'  => 'text',
            'name' => 'comments',
            'attributes' => [
                'id' => 'comments',
                'class' => "input input-bordered w-full",
                'required' => true
            ],
        ]);

       /* $this->add([
            'type' => Element\Select::class,
            'name' => 'status-type',
            'attributes' => [

                'id' => 'status-type',
                'class' => "custom-select form-control",
                'required' => false
            ],
            'options' => [
                'label' => 'Status',
                'id' => 'pos-event-status-type-id',
                'use_hidden_element' => true,
                'value_options' => $this->statusTypes,
            ],
        ]);*/

        // Add the CSRF field
        $this->add([
            'type' => 'csrf',
            'name' => 'shortened_url_csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ],
        ]);

        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'shortened-url-submit',
            'attributes' => [
                'id' => 'shortened-url-submit',
                'required' => true,
                'value' => 'Submit',
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

        // Add input for "full-url" field
        $inputFilter->add([
            'name'     => 'full-url',
            'required' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => UriValidator::class
                ],
            ],
        ]);

        // Add input for "shortened-url" field
        $inputFilter->add([
            'name'     => 'shortened-url',
            'required' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => StringValidator::class,
                    'options' => [
                        'type' => 'shortened-url',
                        'max' => 50, // Maximum length for username
                    ],
                ],
            ],
        ]);

        // Add input for "description" field
        $inputFilter->add([
            'name'     => 'description',
            'required' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => StringValidator::class,
                    'options' => [
                        'type' => 'description',
                        'max' => 50, // Maximum length for username
                    ],
                ],
            ],
        ]);
        // Add input for "comments" field
        $inputFilter->add([
            'name'     => 'comments',
            'required' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => StringValidator::class,
                    'options' => [
                        'type' => 'comments',
                        'max' => 50, // Maximum length for username
                    ],
                ],
            ],
        ]);
    }
}

<?php

// NameValidator.php

namespace Application\Validator;

use Laminas\Validator\AbstractValidator;

class NameValidator extends AbstractValidator
{
    const INVALID_FORMAT = 'invalidFormat';
    const STRING_EMPTY = 'stringEmpty';
    const ALREADY_EXISTS = 'alreadyExists';

    protected $options = [
        'max' => 50, // Default max length if not provided
        'type' => 'first', // Default type if not provided
        'entityManager' => null,
        'itemType' => null,
    ];

    protected $messageTemplates = [
        self::INVALID_FORMAT => "Invalid format for '%value%' name. Only letters, spaces, hyphens, and apostrophes are allowed.",
        self::STRING_EMPTY => "'%value%' name is required.",
        self::ALREADY_EXISTS => "'%value%' already exists.",
    ];

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (isset($options['entityManager'])) {
            $this->setEntityManager($options['entityManager']);
        }

        if (isset($options['itemType'])) {
            $this->setItemtype($options['itemType']);
        }
    }

    public function setEntityManager($entityManager)
    {
        $this->options['entityManager'] = $entityManager;
    }

    public function setItemtype($itemType)
    {
        $this->options['itemType'] = $itemType;
    }

    public function isValid($value)
    {
        $this->setValue($value);

        if (! is_string($value)) {
            $this->error(self::INVALID_FORMAT);
            return false;
        }

        // Trim and check if empty
        $value = trim($value);
        if (empty($value)) {
            $this->error(self::STRING_EMPTY);
            return false;
        }

        // Check length
        $max = isset($this->options['max']) ? $this->options['max'] : 50;
        if (mb_strlen($value) > $max) {
            $this->error(self::INVALID_FORMAT);
            return false;
        }

        // Additional validation logic based on type
        $type = isset($this->options['type']) ? $this->options['type'] : 'user-first-name';
        switch ($type) {
            case 'user-first-name':
            case 'user-last-name':
                // Validate first name or last name format (only letters, spaces, hyphens)
                if (! preg_match('/^[a-zA-Z\s-]+$/', $value)) {
                    $this->error(self::INVALID_FORMAT);
                    return false;
                }
                break;
            case 'user-middle-name':
                // Validate middle name format (only letters, spaces, hyphens, and apostrophes)
                if (! preg_match('/^[a-zA-Z\s\-\'\’]+$/', $value)) {
                    $this->error(self::INVALID_FORMAT);
                    return false;
                }
                break;
            case 'user-name':
                // Validate user name format (only letters, spaces, hyphens)
                if (! preg_match('/^[a-zA-Z\s-]+$/', $value)) {
                    $this->error(self::INVALID_FORMAT);
                    return false;
                }
                break;
            default:
                // Invalid type provided
                $this->error(self::INVALID_FORMAT);
                return false;
        }

        return true;
    }
}

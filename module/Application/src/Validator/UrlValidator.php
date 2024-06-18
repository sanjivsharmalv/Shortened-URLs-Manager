<?php

// NameValidator.php

namespace Application\Validator;

use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Uri as LaminasUriValidator;

class UrlValidator extends AbstractValidator
{
    private const INVALID_URI = 'invalidUri';

    protected $messageTemplates = [
        self::INVALID_URI => "Invalid URI format",
    ];

    public function isValid($value)
    {
        $validator = new LaminasUriValidator();

        if (!$validator->isValid($value)) {
            $this->error(self::INVALID_URI);
            return false;
        }

        return true;
    }
}

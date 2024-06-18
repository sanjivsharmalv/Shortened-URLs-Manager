<?php

// PasswordMatchValidator.php

namespace Application\Validator;

use Laminas\Validator\AbstractValidator;

class PasswordMatchValidator extends AbstractValidator
{
    const NOT_MATCH = 'notMatch';

    protected $messageTemplates = [
        self::NOT_MATCH => 'Passwords do not match.',
    ];

    public function isValid($value, $context = null)
    {

        //match passwords for password and confirm password fields
        $password = $context['user-password'] ?? null;
        $confirmPassword = $context['user-confirm-password'] ?? null;

        if ($password !== $confirmPassword) {
            $this->error(self::NOT_MATCH);
            return false;
        }

        return true;
    }
}

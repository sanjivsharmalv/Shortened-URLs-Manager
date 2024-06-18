<?php
// PasswordValidator.php
// PasswordValidator.php

namespace Application\Validator;

use Laminas\Validator\AbstractValidator;

class PasswordValidator extends AbstractValidator
{
    const TOO_SHORT = 'tooShort';
    const TOO_LONG = 'tooLong';

    protected $messageTemplates = [
        self::TOO_SHORT => "Password must be at least %min% characters long.",
        self::TOO_LONG => "Password must not exceed %max% characters.",
    ];

    protected $options = [
        'min' => 8,
        'max' => 20,
    ];

    protected $minimum = 0;
    protected $maximum = 0;

    protected $messageVariables = array(
        'min' => 'minimum',
        'max' => 'maximum',
    );

    public function isValid($value)
    {
        $this->setValue($value);

        $length = mb_strlen($value);
        $this->minimum = $min = $this->options['min'] ?? 8;
        $this->maximum = $max = $this->options['max'] ?? 20;

        if ($length < $min) {
            $this->error(self::TOO_SHORT, $min);
            return false;
        }

        if ($length > $max) {
            $this->error(self::TOO_LONG, $max);
            return false;
        }

        return true;
    }
}

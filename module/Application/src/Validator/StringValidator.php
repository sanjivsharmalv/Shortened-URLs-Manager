<?php
namespace Application\Validator;

use Laminas\Validator\AbstractValidator;

class StringValidator extends AbstractValidator
{
    const INVALID = 'invalid';

    protected $messageTemplates = [
        self::INVALID => "Invalid string provided",
    ];

    protected $options = [
        'minLength' => 0,
        'maxLength' => null,
        'allowEmpty' => false,
        'alphaOnly' => false,
    ];

    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->options = array_merge($this->options, $options);
        }
        parent::__construct($options);
    }

    public function isValid($value)
    {
        $value = (string) $value;

        if (!$this->options['allowEmpty'] && empty($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $length = mb_strlen($value);

        if ($this->options['minLength'] !== null && $length < $this->options['minLength']) {
            $this->error(self::INVALID);
            return false;
        }

        if ($this->options['maxLength'] !== null && $length > $this->options['maxLength']) {
            $this->error(self::INVALID);
            return false;
        }

        if ($this->options['alphaOnly'] && !ctype_alpha($value)) {
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}
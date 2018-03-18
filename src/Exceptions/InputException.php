<?php

namespace Firehed\Input\Exceptions;

use UnexpectedValueException;

class InputException extends UnexpectedValueException {

    const PARSE_ERROR = 1;
    const FORMAT_ERROR = 2;
    const MISSING_VALUES = 3;
    const INVALID_VALUES = 4;
    const UNEXPECTED_VALUES = 5;

    private $missing = [];
    private $invalid = [];
    private $unexpected = [];

    public function __construct($code, array $errors = []) {
        switch ($code) {
        case self::PARSE_ERROR:
            $msg = 'Input could not be parsed';
            break;
        case self::FORMAT_ERROR:
            $msg = 'Input has a formatting error';
            break;
        case self::MISSING_VALUES:
            $msg = 'Missing required parameter';
            $this->missing = $errors;
            break;
        case self::INVALID_VALUES:
            $msg = 'Invalid parameter';
            $this->invalid = $errors;
            break;
        case self::UNEXPECTED_VALUES:
            $msg = 'Unexpected parameter';
            $this->unexpected = $errors;
            break;
        default:
            throw new \LogicException("Invalid exception code");
            break;
        }
        parent::__construct($msg, $code);
    } // __construct

    public function getMissing(): array
    {
        return $this->missing;
    }

    public function getInvalid(): array
    {
        return $this->invalid;
    }

    public function getUnexpected(): array
    {
        return $this->unexpected;
    }
}

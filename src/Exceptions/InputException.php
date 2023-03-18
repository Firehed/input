<?php

declare(strict_types=1);

namespace Firehed\Input\Exceptions;

use LogicException;
use UnexpectedValueException;

class InputException extends UnexpectedValueException
{

    public const PARSE_ERROR = 1;
    public const FORMAT_ERROR = 2;
    public const MISSING_VALUES = 3;
    public const INVALID_VALUES = 4;
    public const UNEXPECTED_VALUES = 5;
    public const MULTIPLE_VALUE_ERRORS = 6;

    /**
     * @var string[]
     */
    private $missing = [];
    /**
     * @var string[]
     */
    private $invalid = [];
    /**
     * @var string[]
     */
    private $unexpected = [];

    /**
     * @throws LogicException
     */
    public function __construct(int $code, array $errors = [])
    {
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
            case self::MULTIPLE_VALUE_ERRORS:
                $msg = 'Multiple validation errors';
                $this->missing = $errors['missing'] ?? [];
                $this->invalid = $errors['invalid'] ?? [];
                $this->unexpected = $errors['unexpected'] ?? [];
                break;
            default:
                throw new LogicException("Invalid exception code");
        }
        parent::__construct($msg, $code);
    }

    /**
     * @return string[]
     */
    public function getMissing(): array
    {
        return $this->missing;
    }

    /**
     * @return string[]
     */
    public function getInvalid(): array
    {
        return $this->invalid;
    }

    /**
     * @return string[]
     */
    public function getUnexpected(): array
    {
        return $this->unexpected;
    }
}

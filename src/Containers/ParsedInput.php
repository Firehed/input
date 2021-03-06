<?php

declare(strict_types=1);

namespace Firehed\Input\Containers;

use ArrayAccess;
use DomainException;
use BadMethodCallException;
use UnexpectedValueException;
use Firehed\Input\Exceptions\InputException;
use Firehed\Input\Interfaces\ValidationInterface;

/**
 * @implements ArrayAccess<array-key, mixed>
 */
class ParsedInput extends RawInput implements ArrayAccess
{
    /**
     * @param array<array-key, mixed> $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->setIsParsed(true);
    }

    /**
     * @param ParsedInput $add data to add
     * @return $this
     * @throws BadMethodCallException
     */
    public function addData(ParsedInput $add): self
    {
        if ($this->isValidated()) {
            throw new BadMethodCallException(
                "Data cannot be added after validation is performed"
            );
        }
        if ($add->isValidated()) {
            throw new BadMethodCallException(
                "Data cannot be added after validation is performed"
            );
        }

        $this->setData(array_merge($add->getData(), $this->getData()));
        return $this;
    }

    /**
     * @param ValidationInterface $validator Validation requirements
     * @return SafeInput
     * @throws InputException
     */
    public function validate(ValidationInterface $validator): SafeInput
    {

        $data = $this->getData();
        $clean_out = [];
        $missing = [];
        $invalid = [];
        $unexpected = [];

        foreach ($validator->getRequiredInputs() as $key => $input) {
            if (!isset($data[$key]) && !array_key_exists($key, $data)) {
                $missing[] = $key;
                continue;
            }

            try {
                $clean_out[$key] = $input->setValue($data[$key])
                    ->evaluate();
            } catch (InputException $e) {
                $prefix = function ($k) use ($key) {
                    return $key . '.' . $k;
                };
                $invalid = array_merge($invalid, array_map($prefix, $e->getInvalid()));
                $missing = array_merge($missing, array_map($prefix, $e->getMissing()));
                $unexpected = array_merge($unexpected, array_map($prefix, $e->getUnexpected()));
            } catch (UnexpectedValueException $e) {
                $invalid[] = $key;
            } finally {
                unset($data[$key]);
            }
        } unset($key, $input);

        foreach ($validator->getOptionalInputs() as $key => $input) {
            if (array_key_exists($key, $data)) {
                try {
                    $clean_out[$key] = $input->setValue($data[$key])
                        ->evaluate();
                } catch (InputException $e) {
                    $prefix = function ($k) use ($key) {
                        return $key . '.' . $k;
                    };
                    $invalid = array_merge($invalid, array_map($prefix, $e->getInvalid()));
                    $missing = array_merge($missing, array_map($prefix, $e->getMissing()));
                    $unexpected = array_merge($unexpected, array_map($prefix, $e->getUnexpected()));
                } catch (UnexpectedValueException $e) {
                    $invalid[] = $key;
                } finally {
                    unset($data[$key]);
                }
            } else {
                $clean_out[$key] = $input->getDefaultValue();
                unset($data[$key]); // in case of literal null value
            }
        } unset($key, $input);

        $unexpected = array_merge($unexpected, array_keys($data));

        // This is a not-beautiful way of expressing "if at least two error
        // arrays are nonempty", since this should retain the more specific
        // exception code when only one type of error is present.
        if (($missing && ($invalid || $unexpected)) || ($invalid && $unexpected)) {
            throw new InputException(
                InputException::MULTIPLE_VALUE_ERRORS,
                compact('invalid', 'missing', 'unexpected')
            );
        }

        if ($missing) {
            throw new InputException(InputException::MISSING_VALUES, $missing);
        }
        if ($invalid) {
            throw new InputException(InputException::INVALID_VALUES, $invalid);
        }
        if ($unexpected) {
            throw new InputException(InputException::UNEXPECTED_VALUES, $unexpected);
        }

        $this->setData($clean_out);
        $this->setIsValidated(true);

        return new SafeInput($this);
    }

    /**
     * Return the data as an array (this loses the metadata around
     * parsing and validating)
     *
     * @return array<mixed>
     */
    public function asArray(): array
    {
        return $this->getData();
    }

   // ----(ArrayAccess)-------------------------------------------------------

    /**
     * Invoked via `isset` and `empty`
     */
    public function offsetExists($offset)
    {
        throw new BadMethodCallException(
            "ParsedInput is already validated, and contains all expected " .
            "keys. Use standard binary comparitors on the values."
        );
    }

    /**
     * Invoked by array access of the object
     * @return mixed
     */
    public function offsetGet($offset)
    {
        $data = $this->getData();
        if (
            isset($data[$offset]) ||
            array_key_exists($offset, $data)
        ) {
            return $data[$offset];
        }
        throw new DomainException(
            "You are trying to access a value which does not exist. Because " .
            "the input is already validated, this means there is a bug in " .
            "the code. Most likely, there is a typo in the key."
        );
    }

    /**
     * Invoked by setting an array value on the object
     */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException("ParsedInput is read-only");
    }

    /**
     * Invoked via `unset`
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException("ParsedInput is read-only");
    }
}

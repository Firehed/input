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
class ParsedInput implements ArrayAccess
{
    /** @var bool */
    protected $isValidated = false;

/**
     * @var mixed[]
     */
    protected $data;

    /**
     * @param array<array-key, mixed> $data
     *
     * @api
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param ParsedInput $add data to add
     * @return $this
     * @throws BadMethodCallException
     *
     * @api
     */
    public function addData(ParsedInput $add): self
    {
        if ($this->isValidated) {
            throw new BadMethodCallException(
                "Data cannot be added after validation is performed"
            );
        }
        if ($add->isValidated) {
            throw new BadMethodCallException(
                "Data cannot be added after validation is performed"
            );
        }

        $this->data = array_merge($add->data, $this->data);
        return $this;
    }

    /**
     * @param ValidationInterface $validator Validation requirements
     * @return SafeInput
     * @throws InputException
     *
     * @api
     */
    public function validate(ValidationInterface $validator): SafeInput
    {

        $data = $this->data;
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

        $this->data = $clean_out;
        $this->isValidated = true;

        return new SafeInput($this);
    }

    /**
     * Return the data as an array (this loses the metadata around
     * parsing and validating)
     *
     * @return array<mixed>
     *
     * @api
     */
    public function asArray(): array
    {
        return $this->data;
    }

   // ----(ArrayAccess)-------------------------------------------------------

    /**
     * Invoked via `isset` and `empty`
     *
     * @return never
     *
     * @api
     */
    public function offsetExists($offset): bool
    {
        throw new BadMethodCallException(
            "ParsedInput is already validated, and contains all expected " .
            "keys. Use standard binary comparitors on the values."
        );
    }

    /**
     * Invoked by array access of the object
     *
     * @return mixed
     *
     * @api
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (array_key_exists($offset, $this->data)) {
            return $this->data[$offset];
        }
        throw new DomainException(
            "You are trying to access a value which does not exist. Because " .
            "the input is already validated, this means there is a bug in " .
            "the code. Most likely, there is a typo in the key."
        );
    }

    /**
     * Invoked by setting an array value on the object
     *
     * @return never
     *
     * @api
     */
    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException("ParsedInput is read-only");
    }

    /**
     * Invoked via `unset`
     *
     * @return never
     *
     * @api
     */
    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException("ParsedInput is read-only");
    }
}

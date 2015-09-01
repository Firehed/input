<?php

namespace Firehed\Input\Containers;

use DomainException;
use BadMethodCallException;
use UnexpectedValueException;
use Firehed\Input\Exceptions\InputException;
use Firehed\Input\Interfaces\ValidationInterface;

class ParsedInput extends RawInput implements \ArrayAccess {

    public function __construct(array $data) {
        $this->setData($data)
            ->setIsParsed(true);
    } // __construct

    /**
     * @param array data to add
     * @return this
     * @throws BadMethodCallException
     */
    public function addData(ParsedInput $add) {
        if ($this->isValidated()) {
            throw new BadMethodCallException(
                "Data cannot be added after validation is performed");
        }
        if ($add->isValidated()) {
            throw new BadMethodCallException(
                "Data cannot be added after validation is performed");
        }

        $this->setData(array_merge($add->getData(), $this->getData()));
        return $this;
    } // addData

    /**
     * @param Firehed\Input\Interfaces\ValidationInterface Validation requirements
     * @return Firehed\Input\Containers\ValidInput
     * @throws Firehed\Input\Exceptions\InputException
     */
    public function validate(ValidationInterface $validator) {

        $data = $this->getData();
        $clean_out = [];
        $missing = [];
        $invalid = [];


        foreach ($validator->getRequiredInputs() as $key => $input) {
            if (!isset($data[$key]) && !array_key_exists($key, $data)) {
                $missing[] = $key;
                continue;
            }

            try {
                $clean_out[$key] = $input->setValue($data[$key])
                    ->evaluate();
                unset($data[$key]);
            }
            catch (UnexpectedValueException $e) {
                $invalid[] = $key;
            }
        } unset($key, $input);

        foreach ($validator->getOptionalInputs() as $key => $input) {
            if (isset($data[$key])) {
                try {
                    $clean_out[$key] = $input->setValue($data[$key])
                        ->evaluate();
                    unset($data[$key]);
                }
                catch (UnexpectedValueException $e) {
                    $invalid[] = $key;
                }
            }
            else {
                // Somehow, there should be a concept of "use default
                // value" (null unless overridden) so that optional inputs
                // can correctly be resolved as dependencies
                $clean_out[$key] = null;
                unset($data[$key]); // in case of literal null value
            }
        } unset($key, $input);

        if ($missing) {
            throw new InputException(InputException::MISSING_VALUES, $missing);
        }
        if ($invalid) {
            throw new InputException(InputException::INVALID_VALUES, $invalid);
        }
        if ($data) {
            throw new InputException(InputException::UNEXPECTED_VALUES, $data);
        }

        $this->setData($clean_out);
        $this->setIsValidated(true);

        return new SafeInput($this);
    }

    /**
     * Return the data as an array (this loses the metadata around
     * parsing and validating)
     *
     * @return array
     */
    public function asArray() {
        return $this->getData();
    } // asArray

   // ----(ArrayAccess)-------------------------------------------------------

    /**
     * Invoked via `isset` and `empty`
     */
    public function offsetExists($offset) {
        throw new BadMethodCallException(
            "ParsedInput is already validated, and contains all expected ".
            "keys. Use standard binary comparitors on the values.");
    } // offsetExists

    /**
     * Invoked by array access of the object
     * @return mixed
     */
    public function offsetGet($offset) {
        $data = $this->getData();
        if (isset($data[$offset]) ||
            array_key_exists($offset, $data)) {
            return $data[$offset];
        }
        throw new DomainException(
            "You are trying to access a value which does not exist. Because ".
            "the input is already validated, this means there is a bug in ".
            "the code. Most likely, there is a typo in the key.");
    } // offsetGet

    /**
     * Invoked by setting an array value on the object
     */
    public function offsetSet($offset, $value) {
        throw new BadMethodCallException("ParsedInput is read-only");
    } // offsetSet

    /**
     * Invoked via `unset`
     */
    public function offsetUnset($offset) {
        throw new BadMethodCallException("ParsedInput is read-only");
    } // offsetUnset

}

<?php

namespace Firehed\Input\Containers;

use DomainException;
use BadMethodCallException;
use UnexpectedValueException;

use Firehed\Input\Interfaces\SanitizerProviderInterface;

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
     * @param Firehed\Input\Interfaces\SanitizerProviderInterface
     * @return Firehed\Input\Containers\SanitizedInput
     * @throws Firehed\Input\Exceptions\InputException
     */
    public function sanitize(SanitizerProviderInterface $provider) {
        $sanitizers = $provider->getSanitizationFilters();
        assert_instances_of($sanitizers, 'Firehed\Input\Interfaces\SanitizerInterface');
        foreach ($sanitizers as $sanitizer) {
            $this->setData($sanitizer->sanitize($this->getData()));
        }
        $this->setIsSanitized(true);
        return new SanitizedInput($this);
    } // sanitize

    /**
     * Return the data as an array (this loses the metadata around
     * parsing/sanitizing/validating)
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

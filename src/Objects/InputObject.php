<?php

declare(strict_types=1);

namespace Firehed\Input\Objects;

/**
 * The base class for validated and evaluated input data
 *
 * Usage:
 *   $io = new SubclassOfInputObject;
 *   // configure $io if necessary
 *
 *   try {
 *     $validated_value = $io->setValue($raw_value)->evaluate();
 *   }
 *   catch (\UnexpectedValueException $e) {
 *     // value was invalid
 *   }
 *   catch (\Exception $e) {
 *     // you are using it wrong
 *   }
 *   // have fun with $validated_value
 */
abstract class InputObject
{

    private $defaultValue;
    private $value;
    private $valueWasSet = false; // false-like values can be valid, so explicitly track if the setter has been called
    private $isValid;

    public function __construct()
    {
        // No-op, only here so parent::__construct won't fatal
    } // __construct

    /**
     * Get the default value for the object
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set a default value, which will be provided only during optional input
     * value validation
     *
     * @param mixed $value The default value
     * @return $this
     */
    public function setDefaultValue($value): self
    {
        $this->defaultValue = $value;
        return $this;
    }

    /**
     * Protected because this should only be called by children during
     * `evaluate`
     */
    final protected function getValue() {
        if (!$this->isValid()) {
            throw new \UnexpectedValueException("Value is invalid");
        }
        return $this->value;
    } // getValue

    /**
     * @param mixed value to validate
     * @return self
     */
    final public function setValue($value): self {
        $this->isValid = null;
        $this->value = $value;
        $this->valueWasSet = true;
        return $this;
    } // setValue

    /**
     * @return bool
     */
    abstract protected function validate($value): bool;

    /**
     * @return bool
     */
    final public function isValid(): bool {
        if (null === $this->isValid) {
            if (!$this->valueWasSet) {
                throw new \BadMethodCallException("Value has not been set");
            }

            $this->isValid = $this->validate($this->value);
        }
        return $this->isValid;
    } // isValid

    /**
     * @return mixed
     */
    public function evaluate() {
        return $this->getValue();
    } // evaluate

}

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
 *
 * @template ValidatedType
 */
abstract class InputObject
{
    private mixed $defaultValue = null;
    private mixed $value;

    /**
     * false-like values can be valid, so explicitly track if the setter has been called
     */
    // private bool $valueWasSet = false;

    /**
     * isValid tracks both a) if the value has been validated, and b) if it's
     * actually valid.
     */
    private ?bool $isValid = null;

    /**
     * Extending classes must implement the `validate` method, accepting the
     * arbitrary value from the input being validated and returning a boolean
     * indicating if the value is valid.
     */
    abstract protected function validate(mixed $value): bool;

    public function __construct()
    {
        // No-op, only here so parent::__construct won't fatal
    }

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
     *
     * @return mixed
     */
    final protected function getValue()
    {
        if (!$this->isValid()) {
            throw new \UnexpectedValueException("Value is invalid");
        }
        return $this->value;
    }

    /**
     * @param mixed $value value to validate
     * @return $this
     */
    final public function setValue(mixed $value): self
    {
        $this->isValid = null;
        $this->value = $value;
        return $this;
    }

    final public function isValid(): bool
    {
        if (null === $this->isValid) {
            $this->isValid = $this->validate($this->value);
        }
        return $this->isValid;
    }

    /**
     * @return mixed
     */
    public function evaluate()
    {
        return $this->getValue();
    }
}

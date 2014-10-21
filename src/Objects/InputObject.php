<?php

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
abstract class InputObject {

    private $value;
    private $valueWasSet = false; // false-like values can be valid, so explicitly track if the setter has been called
    private $isValid;
    private $dependencies = [];

    // No-op, only here so parent::__construct won't fatal
    public function __construct() { } // __construct

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
     * @return this
     */
    final public function setValue($value) {
        $this->isValid = null;
        $this->value = $value;
        $this->valueWasSet = true;
        return $this;
    } // setValue

    /**
     * @return bool
     */
    abstract protected function validate($value);

    /**
     * @return bool
     */
    final public function isValid() {
        if (null === $this->isValid) {
            if (!$this->valueWasSet) {
                throw new \BadMethodCallException("Value has not been set");
            }

            $this->isValid = $this->validate($this->value);
        }
        return $this->isValid;
    } // isValid

    /**
     * @param string Key by which dependency will be accessible
     * @param Firehed\Input\Object\InputObject the dependency
     * @return this
     */
    final public function addDependency($key, InputObject $dep) {
        // Todo: sanity check to prevent circular deps?
        // $dep !== this
        // this.dependencies.each(dep not in each's deps=
        $this->dependencies[$key] = $dep;
        return $this;
    } // addDependency

    /**
     * @return bool
     */
    final public function hasUnresolvedDependencies() {
        foreach ($this->dependencies as $dependency) {
            if (!$dependency->valueWasSet) {
                return true;
            }
            if (!$dependency->isValid()) {
                return true;
            }
        }
        return false;
    } // hasUnresolvedDependencies

    /**
     * @return mixed
     */
    public function evaluate() {
        return $this->getValue();
    } // evaluate

}

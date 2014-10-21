<?php

namespace Firehed\Input\Objects;

use InvalidArgumentException;

class Number extends InputObject {

    private $min = null;
    private $max = null;

    public function setMin($min) {
        if (!is_int($min) && !is_float($min)) {
            throw new InvalidArgumentException(
                'Minimum must be an integer or float');
        }
        if (null !== $this->max && $this->max < $min) {
            throw new InvalidArgumentException(
                "Minimum cannot be greater than maximum");
        }

        $this->min = $min;
        return $this;
    } // setMin

    public function setMax($max) {
        if (!is_int($max) && !is_float($max)) {
            throw new InvalidArgumentException(
                'Maximum must be an integer or float');
        }
        if (null !== $this->min && $this->min > $max) {
            throw new InvalidArgumentException(
                "Maximum cannot be less than minimum");
        }
        $this->max = $max;
        return $this;
    } // setMax

    protected function validate($value) {
        if (!is_numeric($value)) {
            return false;
        }
        // This is effectively a cast, but less lossy (see tests)
        $value = $value + 0;
        if (null !== $this->min) {
            if ($value < $this->min) {
                return false;
            }
        }
        if (null !== $this->max) {
            if ($value > $this->max) {
                return false;
            }
        }
        return true;
    } // validate

    public function evaluate() {
        return $this->getValue() + 0;
    } // evaluate

}

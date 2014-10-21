<?php

namespace Firehed\Input\Objects;

use InvalidArgumentException;

class String extends InputObject {

    private $min = null;
    private $max = null;

    public function setMin($min) {
        if (!is_int($min)) {
            throw new InvalidArgumentException(
                "Integer required");
        }
        if ($min < 0) {
            throw new InvalidArgumentException(
                "Minimum cannot be less than zero");
        }

        if (null !== $this->max && $this->max < $min) {
            throw new InvalidArgumentException(
                "Minimum cannot be greater than maximum");
        }

        $this->min = $min;
        return $this;
    } // setMin

    public function setMax($max) {
        if (!is_int($max)) {
            throw new InvalidArgumentException(
                "Integer required");
        }
        if ($max <= 0) {
            throw new InvalidArgumentException(
                "Maximum cannot be less than one");
        }
        if (null !== $this->min && $this->min > $max) {
            throw new InvalidArgumentException(
                "Maximum cannot be less than minimum");
        }
        $this->max = $max;
        return $this;
    } // setMax

    protected function validate($value) {
        if (!is_string($value)) {
            return false;
        }
        if (null !== $this->min) {
            if (strlen($value) < $this->min) {
                return false;
            }
        }
        if (null !== $this->max) {
            if (strlen($value) > $this->max) {
                return false;
            }
        }
        return true;
    }

}

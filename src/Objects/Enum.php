<?php

namespace Firehed\Input\Objects;

abstract class Enum extends InputObject {

    /**
     * @param mixed value to validate
     * @return bool
     */
    final protected function validate($value) {
        return in_array($value, $this->getValidValues());
    } // validate

    /**
     * @return array<string>
     */
    abstract protected function getValidValues();

}

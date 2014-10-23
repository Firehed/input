<?php

namespace Firehed\Input\Objects;

use Firehed\Input\Containers\ParsedInput;
use Firehed\Input\Exceptions\InputException;
use Firehed\Input\Interfaces\ValidationInterface;

abstract class Structure extends InputObject implements ValidationInterface {

    private $validated;

    protected function validate($value) {
        if (!is_array($value)) {
            return false;
        }
        $parsed = new ParsedInput($value);
        try {
            $this->validated = $parsed
                ->sanitize([]) // Sanitized already, tis is safe
                ->validate($this);
            return true;
        }
        catch (InputException $e) {
            return false;
        }
    } // validate

    public function evaluate() {
        // Performs validation
        parent::evaluate();
        return $this->validated->asArray();
    } // evaluate

}

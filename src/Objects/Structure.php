<?php

namespace Firehed\Input\Objects;

use Firehed\Input\Containers\ParsedInput;
use Firehed\Input\Exceptions\InputException;
use Firehed\Input\Interfaces\ValidationInterface;
use Firehed\Input\Interfaces\SanitizerProviderInterface;

abstract class Structure extends InputObject
    implements ValidationInterface,
    SanitizerProviderInterface {

    private $validated;

    protected function validate($value) {
        if (!is_array($value)) {
            return false;
        }
        $parsed = new ParsedInput($value);
        try {
            $this->validated = $parsed
                ->sanitize($this)
                ->validate($this);
            return true;
        }
        catch (InputException $e) {
            return false;
        }
    } // validate

    // Sanitized already, this is safe
    final public function getSanitizationFilters() {
        return [];
    } // getSanitizationFilters

    public function evaluate() {
        // Performs validation
        parent::evaluate();
        return $this->validated->asArray();
    } // evaluate

}

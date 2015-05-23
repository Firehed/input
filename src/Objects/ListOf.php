<?php

namespace Firehed\Input\Objects;

/**
 * The ListOf InputObject is used in situations where we expect to receive
 * an array of some other input type. Unlike most other InputObjects, it
 * requires explicit configuration via
 * @{method:setType@Firehed.Input.Objects\InputObject}. Conceptually, it applies
 * @{function@libphutil:assert_instances_of} to each item in the provided
 * array.
 */
class ListOf extends InputObject {

    private $type;

    public function __construct(InputObject $type) {
        $this->type = $type;
    } // __construct

    protected function validate($value) {
        if (!is_array($value)) {
            return false;
        }
        // Todo: support min/max on count?

        foreach ($value as $key => $item) {
            // A dictionary was passed, no good
            if (!is_int($key)) {
                return false;
            }
            if (!$this->type->setValue($item)->isValid()) {
                return false;
            }
        }
        return true;
    } // validate

    public function evaluate() {
        $values = parent::evaluate();
        foreach ($values as $key => $value) {
            $values[$key] = $this->type->setValue($value)->evaluate();
        }
        return $values;
    } // evaluate

}

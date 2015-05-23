<?php

namespace Firehed\Input\Sanitizers;

class XSS implements \Firehed\Input\Interfaces\SanitizerInterface {

    public function sanitize(array $input) {
        $output = [];
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $output[$key] = $this->sanitize($value);
            }
            elseif (is_object($value)) {
                // Likely scenario: JSON decode to StdClass instead of
                // associative array
                throw new \InvalidArgumentException(sprintf(
                    'An object was provided to %s at key [%s]',
                    __METHOD__,
                    $key));
            }
            elseif (is_string($value)) {
                $output[$key] = htmlspecialchars($value, \ENT_QUOTES, 'UTF-8');
            }
            // Non-string scalars are, by definition, safe to store or render
            else {
                $output[$key] = $value;
            }
        }
        return $output;
    } // sanitize

}

<?php

namespace Firehed\Input\Interfaces;

interface SanitizerInterface {

    /**
     * @param array Input array to be sanitized
     * @return array Sanitized output
     */
    public function sanitize(array $input);
}

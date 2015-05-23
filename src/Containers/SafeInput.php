<?php

namespace Firehed\Input\Containers;

use BadMethodCallException;

/**
 * Overall usage:
 *
 *   $safe_input = new RawInput($raw_data)
 *     ->parse($parser)
 *     ->sanitize([$sanitizer_one, $sanitizer_two])
 *     ->validate($validator);
 */
class SafeInput extends SanitizedInput {

    public function __construct(SanitizedInput $valid) {
        if (!$valid->isValidated()) {
            throw new BadMethodCallException;
        }
        $this->setData($valid->getData());
    } // __construct

}

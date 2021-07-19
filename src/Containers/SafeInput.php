<?php

namespace Firehed\Input\Containers;

use BadMethodCallException;

/**
 * Overall usage:
 *
 *   $safe_input = new RawInput($raw_data)
 *     ->parse($parser)
 *     ->validate($validator);
 */
class SafeInput extends ParsedInput
{

    public function __construct(ParsedInput $valid)
    {
        if (!$valid->isValidated()) {
            throw new BadMethodCallException();
        }
        parent::__construct($valid->getData());
    }
}

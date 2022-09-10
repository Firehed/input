<?php

declare(strict_types=1);

namespace Firehed\Input\V3;

/**
 * Allows `null` in addition to the specified input type. This is NOT
 * a substitute for a missing k/v pair in a dictionary. If the literal `null`
 * is provided then the default value from the constructor will be returned
 * (which is, itself, `null`). Any other value will be validated according to
 * the wrapped validator.
 *
 * @template Wrapped
 * @template Default
 * @implements Validator<Wrapped & Default>
 */
class Nullable implements Validator
{
    /**
     * @param Validator<Wrapped> $validator
     * @param Default $default
     */
    public function __construct(private Validator $validator, private mixed $default = null)
    {
    }

    public function validate(mixed $input): Result
    {
        if ($input === null) {
            return Result::ok($this->default);
        }
        return $this->validator->validate($input);
    }
}

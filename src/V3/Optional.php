<?php

declare(strict_types=1);

namespace Firehed\Input\V3;

/**
 * This validator serves as a marker for `Dict` to permit entirely-missing
 * key-value pairs and fill in the default value. The default value will *not*
 * apply in the case of a present value which fails validation.
 *
 * @template V
 * @template Default
 * @implements Validator<V>
 */
class Optional implements Validator
{
    /**
     * @param Validator<V> $validator
     * @param Default $default
     */
    public function __construct(private Validator $validator, private mixed $default = null)
    {
    }

    public function validate(mixed $input): Result
    {
        return $this->validator->validate($input);
    }

    public function getDefaultValue(): mixed
    {
        return $this->default;
    }
}

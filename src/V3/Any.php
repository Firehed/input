<?php

declare(strict_types=1);

namespace Firehed\Input\V3;

/**
 * @implements Validator<mixed>
 */
class Any implements Validator
{
    public function validate(mixed $input): Result
    {
        return Result::ok($input);
    }
}

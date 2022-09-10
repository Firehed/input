<?php

declare(strict_types=1);

namespace Firehed\Input\V3;

/**
 * @implements Validator<string>
 */
class Email implements Validator
{
    public function validate(mixed $input): Result
    {
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return Result::ok($input);
        } else {
            return Result::error();
        }
    }
}

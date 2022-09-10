<?php

declare(strict_types=1);

namespace Firehed\Input\V3;

/**
 * @template T
 */
interface Validator
{
    /**
     * @return Result<T>
     */
    public function validate(mixed $input): Result;
}

<?php

declare(strict_types=1);

namespace Firehed\Input\Validators;

/**
 * @template T
 */
interface ValidatorInterface
{
    /**
     * @return Result<T>
     */
    public function validate(mixed $decoded): Result;
}

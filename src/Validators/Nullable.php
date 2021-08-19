<?php

declare(strict_types=1);

namespace Firehed\Input\Validators;

/**
 * @template NonNullable
 * @implements ValidatorInterface<NonNullable | null>
 */
class Nullable implements ValidatorInterface
{
    /**
     * @param ValidatorInterface<NonNullable> $validator
     */
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(mixed $decoded): Result
    {
        if ($decoded === null) {
            return Result::ok(null);
        }
        return $this->validator->validate($decoded);
    }
}

<?php

declare(strict_types=1);

namespace Firehed\Input\Validators;

use function is_iterable;

/**
 * @implements ValidatorInterface<Item[]>
 * @template Item
 */
class ListOf implements ValidatorInterface
{
    /**
     * @param ValidatorInterface<Item> $validator
     */
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(mixed $decoded): Result
    {
        if (!is_iterable($decoded)) {
            return Result::error('Value is not convertable to a list');
        }
        // Normally array_map would be preferred here, but this avoids having
        // to do any additional type juggling (for non-array iterables) or
        // re-keying (for ensuring array_is_list(result))
        $output = [];
        foreach ($decoded as $item) {
            $output[] = $this->validator->validate($item);
        }
        return Result::ok(
            array_map(fn ($item) => $item->unwrap(), $output)
        );
    }
}

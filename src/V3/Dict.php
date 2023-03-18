<?php

declare(strict_types=1);

namespace Firehed\Input\V3;

/**
 * @template TKeys of array-key
 * @template TValues
 *
 * @template TWrapped of array<TKeys, TValues>
 *
 * @implements Validator<TWrapped>
 *
 */
class Dict implements Validator
{
    /**
     * @param array<TKeys, Validator<TValues>> $fields
     */
    public function __construct(
        private array $fields,
        private bool $treatUnexpectedAsError = true,
    ) {
        // Assert that no keys overlap between required and optional
    }

    public function validate(mixed $input): Result
    {
        if (!is_array($input) && !is_object($input)) {
            return Result::error();
        }
        // Cast obj to array for iteration
        $input = (array) $input;

        $missing = $invalid = [];
        $seen = [];
        $ok = [];
        foreach ($this->fields as $key => $validator) {
            $seen[$key] = true;
            if (array_key_exists($key, $input)) {
                $result = $validator->validate($input[$key]);
                if ($result->isOk()) {
                    $ok[$key] = $result->unwrap();
                } else {
                    $invalid[$key] = true;
                }
            } else {
                if ($validator instanceof Optional) {
                    $ok[$key] = $validator->getDefaultValue();
                } else {
                    $missing[] = $key;
                    // $result[$key] = Result::error();
                }
                continue;
            }
        }
        // TODO validate me
        // - check that there are no unexpected values
        // - check no invalid
        // - check no missing
        // - provided details
        if ($invalid || $missing) {
            return Result::error();
        }

        return Result::ok($ok);
    }
}

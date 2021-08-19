<?php

declare(strict_types=1);

namespace Firehed\Input\Containers;

use Firehed\Input\Validators\Result;

/**
 * @template T
 */
class ValidatedInput
{
    /**
     * @param Result<T> $validated
     */
    public function __construct(public Result $validated)
    {
    }

    /**
     * @return T
     */
    public function getData()
    {
        return $this->validated->unwrap();
    }
}

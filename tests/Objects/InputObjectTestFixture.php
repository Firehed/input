<?php

declare(strict_types=1);

namespace Firehed\Input\Objects;

class InputObjectTestFixture extends InputObject
{
    public const MAGIC_FAIL = '198sjs $ a2/';

    public function validate(mixed $value): bool
    {
        return $value !== self::MAGIC_FAIL;
    }
}

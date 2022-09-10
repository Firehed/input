<?php

declare(strict_types=1);

namespace Firehed\Input\V3;

/**
 * @template T of enum
 * @implements Validator<T>
 */
#[PHP81Only]
class Enum implements Validator
{
    /**
     * @param class-string<T> $fqcn
     */
    public function __construct(private string $fqcn)
    {
        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            throw new \LogicException('The enum validator can only be used in PHP>=8.1 with native enum support');
        }
        if (!enum_exists($fqcn)) {
            throw new \DomainException("$fqcn is not a native enum");
        }
    }

    public function validate(mixed $input): Result
    {
        if (is_string($input) || is_int($input)) {
            $result = [$this->fqcn, 'tryFrom']($input);
            if ($result === null) {
                return Result::error();
            }
            return Result::ok($result);
        }
        return Result::error(); // Input cannot be an enum backed value
    }
}

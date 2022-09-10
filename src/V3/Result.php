<?php

declare(strict_types=1);

namespace Firehed\Input\V3;

/**
 * @template T
 */
class Result
{
    /** @var T */
    private $ok;
    private bool $isOk;

    private function __construct()
    {
    }

    /**
     * @param T $wrapped
     * @return Result<T>
     */
    public static function ok(mixed $wrapped): Result
    {
        $result = new Result();
        $result->isOk = true;
        $result->ok = $wrapped;
        return $result;
    }

    public static function error(): Result
    {
        $result = new Result();
        $result->isOk = false;
        return $result;
    }

    public function isOk(): bool
    {
        return $this->isOk;
    }

    /** @return T */
    public function unwrap(): mixed
    {
        assert($this->isOk);
        return $this->ok;
    }
}

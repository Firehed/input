<?php

declare(strict_types=1);

namespace Firehed\Input\Validators;

/**
 * @template Success
 */
class Result
{
    private bool $ok;
    private mixed $success;
    private mixed $error;

    /**
     * @param Success $value
     * @return Result<Success>
     */
    public static function ok(mixed $value): Result
    {
        $result = new Result();
        $result->ok = true;
        $result->success = $value;
        return $result;
    }

    /**
     * @return Result<Success>
     */
    public static function error(mixed $error): Result
    {
        $result = new Result();
        $result->ok = false;
        $result->error = $error;
        return $result;
    }

    public function isOk(): bool
    {
        return $this->ok;
    }

    /**
     * @return Success
     */
    public function unwrap()
    {
        return $this->success;
    }
}

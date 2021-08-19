<?php

declare(strict_types=1);

namespace Firehed\Input\Containers;

use Firehed\Input\Decoders\DecoderInterface;
use Firehed\Input\Validators\ValidatorInterface;

class DecodedInput
{
    private mixed $decoded;

    public function __construct(mixed $raw, DecoderInterface $decoder)
    {
        $this->decoded = $decoder->decode($raw);
    }

    /**
     * @template T
     * @param ValidatorInterface<T> $validator
     * @return ValidatedInput<T>
     */
    public function validate(ValidatorInterface $validator): ValidatedInput
    {
        $result = $validator->validate($this->decoded);
        return new ValidatedInput($result);
    }
}

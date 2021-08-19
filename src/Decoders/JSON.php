<?php

declare(strict_types=1);

namespace Firehed\Input\Decoders;

use Firehed\Input\Exceptions\InputException;

use function json_decode;
use function json_last_error;

use const JSON_THROW_ON_ERROR;

class JSON implements DecoderInterface
{
    public function decode(string $rawInput): mixed
    {
        if ($rawInput === '') {
            return null; // Maybe this should be a parse error?
        }
        $decoded = json_decode($rawInput);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InputException(InputException::PARSE_ERROR);
        }
        return $decoded;
    }

    public function getSupportedMimeTypes(): array
    {
        return [
            'application/json',
        ];
    }
}

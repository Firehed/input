<?php

declare(strict_types=1);

namespace Firehed\Input\Decoders;

use Firehed\Input\Exceptions\InputException;
use Firehed\Input\Interfaces\ParserInterface;

use function is_array;
use function json_decode;
use function json_last_error;
use function strlen;

use const JSON_THROW_ON_ERROR;

class JSON implements ParserInterface, DecoderInterface
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

    /**
     * Legacy behavior always does a json_decode to an associative array. This
     * is a lossy conversion from certain kinds of raw inputs due to PHP array
     * semantics.
     */
    public function parse(string $raw_input): array
    {
        if (!strlen($raw_input)) {
            return [];
        }
        $assoc = true;
        $data = json_decode($raw_input, $assoc);
        if (!$data && json_last_error() != JSON_ERROR_NONE) {
            throw new InputException(InputException::PARSE_ERROR);
        }
        if (!is_array($data)) {
            throw new InputException(InputException::FORMAT_ERROR);
        }
        return $data;
    }

    public function getSupportedMimeTypes(): array
    {
        return [
            'application/json',
        ];
    }
}

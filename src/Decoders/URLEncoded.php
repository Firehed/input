<?php

declare(strict_types=1);

namespace Firehed\Input\Decoders;

use Firehed\Input\Exceptions\InputException;
use Firehed\Input\Interfaces\ParserInterface;

use function is_array;
use function parse_str;
use function strlen;

class URLEncoded implements DecoderInterface
{
    public function decode(string $rawInput): mixed
    {
        if (!strlen($rawInput)) {
            return [];
        }
        $out = [];
        parse_str($rawInput, $out);
        if (!is_array($out) || !$out) {
            throw new InputException(InputException::FORMAT_ERROR);
        }
        return $out;
    }

    public function getSupportedMimeTypes(): array
    {
        return [
            'application/x-www-form-urlencoded',
        ];
    }
}

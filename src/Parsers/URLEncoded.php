<?php

namespace Firehed\Input\Parsers;

use Firehed\Input\Exceptions\InputException;
use Firehed\Input\Interfaces\ParserInterface;

class URLEncoded implements ParserInterface
{

    public function parse(string $raw_input): array
    {
        if (!strlen($raw_input)) {
            return [];
        }
        $out = [];
        parse_str($raw_input, $out);
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

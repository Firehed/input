<?php

namespace Firehed\Input\Parsers;

use Firehed\Input\Exceptions\InputException;
use Firehed\Input\Interfaces\ParserInterface;

class JSON implements ParserInterface
{

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
    } // parse

    public function getSupportedMimeTypes(): array
    {
        return [
            'application/json',
        ];
    } // getSupportedMimeTypes
}

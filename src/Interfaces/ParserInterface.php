<?php

declare(strict_types=1);

namespace Firehed\Input\Interfaces;

use Firehed\Input\Exceptions\InputException;

interface ParserInterface
{

    /**
     * @param string $raw_input Unparsed, unvalidated input
     * @return array Parsed, unvalidated input
     * @throws InputException
     */
    public function parse(string $raw_input): array;

    /**
     * @return array<string> Supported mime types
     */
    public function getSupportedMimeTypes(): array;
}

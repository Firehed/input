<?php

declare(strict_types=1);

namespace Firehed\Input\Parsers;

use Firehed\Input\Exceptions\InputException;

interface ParserInterface
{
    /**
     * @throws InputException
     */
    public function parse(string $rawInput): mixed;

    /**
     * @return string[] Supported mime types
     */
    public function getSupportedMimeTypes(): array;
}

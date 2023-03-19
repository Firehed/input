<?php

declare(strict_types=1);

namespace Firehed\Input\Containers;

use Firehed\Input\Interfaces\ParserInterface;

class RawInput
{
    public function __construct(private string $raw)
    {
    }

    /**
     * @api
     */
    public function parse(ParserInterface $parser): ParsedInput
    {
        $parsed = $parser->parse($this->raw);
        return new ParsedInput($parsed);
    }
}

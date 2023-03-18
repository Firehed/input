<?php

declare(strict_types=1);

namespace Firehed\Input\Containers;

use Firehed\Input\Interfaces\ParserInterface;

class RawInput
{
    /** @var string */
    private $data;

    /**
     * @param string $raw
     */
    public function __construct($raw)
    {
        $this->data = $raw;
    }

    /**
     * @api
     */
    public function parse(ParserInterface $parser): ParsedInput
    {
        $parsed = $parser->parse($this->data);
        return new ParsedInput($parsed);
    }
}

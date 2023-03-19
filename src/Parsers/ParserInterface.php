<?php

declare(strict_types=1);

namespace Firehed\Input\Parsers;

use Firehed\Input\Interfaces\ParserInterface as BaseParserInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ParserInterface extends BaseParserInterface
{
    /**
     * Sets the SRI parsedBody with the parser output
     */
    public function parseRequest(ServerRequestInterface $request): ServerRequestInterface;
}

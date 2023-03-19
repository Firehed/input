<?php

declare(strict_types=1);

namespace Firehed\Input\Parsers;

use Psr\Http\Message\ServerRequestInterface;

trait ParsePsrRequestTrait
{
    public function parseRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        $body = (string) $request->getBody();
        $parsed = $this->parse($body);
        return $request->withParsedBody($parsed);
    }
}

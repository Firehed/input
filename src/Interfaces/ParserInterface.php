<?php

declare(strict_types=1);

namespace Firehed\Input\Interfaces;

interface ParserInterface {

    /**
     * @param string Unparsed, unvalidated input
     * @return array Parsed, unvalidated input
     * @throws \ApiException
     */
    public function parse(string $raw_input): array;

    /**
     * @return array<string> Supported mime types
     */
    public function getSupportedMimeTypes(): array;

}

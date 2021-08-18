<?php

declare(strict_types=1);

namespace Firehed\Input\Interfaces;

use Firehed\Input\Exceptions\InputException;
use Firehed\Input\Decoders\DecoderInterface;

/**
 * @deprecated This interface will be removed in v4. Use
 *             `Firehed\Input\Parsers\ParserInterface` instead.
 */
interface ParserInterface extends DecoderInterface
{
    /**
     * @param string $raw_input Unparsed, unvalidated input
     * @return array<mixed> Parsed, unvalidated input
     * @throws InputException
     */
    public function parse(string $raw_input): array;
}

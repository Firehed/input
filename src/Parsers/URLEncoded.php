<?php

declare(strict_types=1);

namespace Firehed\Input\Parsers;

use Firehed\Input\Decoders\URLEncoded as URLEncodedDecoder;
use Firehed\Input\Exceptions\InputException;
use Firehed\Input\Interfaces\ParserInterface;

/**
 * @deprecated This class will be removed in v4. The replacement is
 * \Firehed\Input\Decoders\URLEncoded.
 */
class URLEncoded extends URLEncodedDecoder implements ParserInterface
{
    public function parse(string $raw_input): array
    {
        return $this->decode($raw_input);
    }
}

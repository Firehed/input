<?php

declare(strict_types=1);

namespace Firehed\Input\Decoders;

use Firehed\Input\Exceptions\InputException;

interface DecoderInterface
{
    /**
     * @throws InputException
     */
    public function decode(string $rawInput): mixed;

    /**
     * @return string[] Supported mime types
     */
    public function getSupportedMimeTypes(): array;
}

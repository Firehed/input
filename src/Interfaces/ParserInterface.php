<?php

declare(strict_types=1);

namespace Firehed\Input\Interfaces;

use Firehed\Input\Parsers\ParserInterface as RealParserInterface;

/**
 * @deprecated This interface will be removed in v4. Use
 *             `Firehed\Input\Parsers\ParserInterface` instead.
 */
interface ParserInterface extends RealParserInterface
{
}

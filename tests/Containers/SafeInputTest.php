<?php

namespace Firehed\Input\Containers;

use BadMethodCallException;

/**
 * @covers Firehed\Input\Containers\SafeInput
 */
class SafeInputTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructThrowsWithUnvalidatedInput(): void
    {
        $parsed = new ParsedInput(['foo' => 'bar']);
        $this->expectException(BadMethodCallException::class);
        new SafeInput($parsed);
    }
}

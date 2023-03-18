<?php

namespace Firehed\Input\Containers;

use Firehed\Input\Interfaces\ParserInterface;
use Firehed\Input\Containers\ParsedInput;

/**
 * @covers Firehed\Input\Containers\RawInput
 */
class RawInputTest extends \PHPUnit\Framework\TestCase
{

    public function testConstruct(): void
    {
        $this->assertInstanceOf(
            'Firehed\Input\Containers\RawInput',
            new RawInput('some raw data'),
            "Construct failed"
        );
    }

    public function testParse(): void
    {
        $raw_data = md5((string)rand());
        $mock = $this->createMock(ParserInterface::class);
        $mock->expects($this->once())
            ->method('parse')
            ->with($raw_data)
            ->will($this->returnValue((array)$raw_data));
        $raw = new RawInput($raw_data);
        $parsed = $raw->parse($mock);
        $this->assertInstanceOf(
            ParsedInput::class,
            $parsed,
            'RawInput::parse() should return a ParsedInput object'
        );
    }
}

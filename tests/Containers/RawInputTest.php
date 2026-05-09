<?php

declare(strict_types=1);

namespace Firehed\Input\Containers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RawInput::class)]
class RawInputTest extends TestCase
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
        $mock = $this->createMock('Firehed\Input\Interfaces\ParserInterface');
        $mock->expects($this->once())
            ->method('parse')
            ->with($raw_data)
            ->willReturn((array)$raw_data);
        $raw = new RawInput($raw_data);
        $parsed = $raw->parse($mock);
        $this->assertInstanceOf(
            'Firehed\Input\Containers\ParsedInput',
            $parsed,
            'RawInput::parse() should return a ParsedInput object'
        );
        $this->assertTrue($raw->isParsed(), 'isParsed should be true');
        $this->assertFalse($raw->isValidated(), 'isValid should be false');
    }
}

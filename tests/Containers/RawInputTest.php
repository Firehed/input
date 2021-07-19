<?php

namespace Firehed\Input\Containers;

/**
 * @coversDefaultClass Firehed\Input\Containers\RawInput
 * @covers ::<protected>
 * @covers ::<private>
 */
class RawInputTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $this->assertInstanceOf(
            'Firehed\Input\Containers\RawInput',
            new RawInput('some raw data'),
            "Construct failed"
        );
    }

    /**
     * @covers ::parse
     */
    public function testParse(): void
    {
        $raw_data = md5((string)rand());
        $mock = $this->createMock('Firehed\Input\Interfaces\ParserInterface');
        $mock->expects($this->once())
            ->method('parse')
            ->with($raw_data)
            ->will($this->returnValue((array)$raw_data));
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

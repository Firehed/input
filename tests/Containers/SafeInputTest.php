<?php

declare(strict_types=1);

namespace Firehed\Input\Containers;

use BadMethodCallException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SafeInput::class)]
class SafeInputTest extends TestCase
{
    /**
     * @param mixed[] $data
     */
    private function getSafeInput(array $data): SafeInput
    {
        $mock = $this->getMockBuilder(ParsedInput::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getData', 'isValidated'])
            ->getMock();
        $mock->expects($this->any())
            ->method('getData')
            ->willReturn($data);
        $mock->expects($this->any())
            ->method('isValidated')
            ->willReturn(true);
        return new SafeInput($mock);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(SafeInput::class, $this->getSafeInput([]));
    }

    public function testConstructThrowsWithUnvalidatedInput(): void
    {
        $valid = $this->getMockBuilder(ParsedInput::class)
            ->disableOriginalConstructor()
            ->getMock();
        $valid->expects($this->atLeastOnce())
            ->method('isValidated')
            ->willReturn(false);
        $this->expectException(BadMethodCallException::class);
        new SafeInput($valid);
    }
}

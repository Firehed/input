<?php

namespace Firehed\Input\Containers;

use BadMethodCallException;

/**
 * @covers Firehed\Input\Containers\SafeInput
 */
class SafeInputTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param mixed[] $data
     */
    private function getSafeInput(array $data): SafeInput
    {
        $mock = $this->getMockBuilder(ParsedInput::class)
            ->disableOriginalConstructor()
            ->setMethods(['getData', 'isValidated'])
            ->getMock();
        $mock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($data));
        $mock->expects($this->any())
            ->method('isValidated')
            ->will($this->returnValue(true));
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
            ->will($this->returnValue(false));
        $this->expectException(BadMethodCallException::class);
        new SafeInput($valid);
    }
}

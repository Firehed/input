<?php

namespace Firehed\Input\Containers;

use BadMethodCallException;

/**
 * @coversDefaultClass Firehed\Input\Containers\SafeInput
 */
class SafeInputTest extends \PHPUnit\Framework\TestCase
{

    private function getSafeInput(array $data)
    {
        $mock = $this->getMockBuilder('Firehed\Input\Containers\ParsedInput')
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

    /**
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $this->assertInstanceOf('Firehed\Input\Containers\SafeInput', $this->getSafeInput([]));
    }

    /**
     * @covers ::__construct
     */
    public function testConstructThrowsWithUnvalidatedInput(): void
    {
        $valid = $this->getMockBuilder('Firehed\Input\Containers\ParsedInput')
            ->disableOriginalConstructor()
            ->getMock();
        $valid->expects($this->atLeastOnce())
            ->method('isValidated')
            ->will($this->returnValue(false));
        $this->expectException(BadMethodCallException::class);
        new SafeInput($valid);
    }
}

<?php

namespace Firehed\Input\Containers;

/**
 * @coversDefaultClass Firehed\Input\Containers\SafeInput
 */
class SafeInputTest extends \PHPUnit_Framework_TestCase {

    private function getSafeInput(array $data) {
        $mock = $this->getMockBuilder('Firehed\Input\Containers\SanitizedInput')
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
    } // getSafeInput

    /**
     * @covers ::__construct
     */
    public function testConstruct() {
        $this->assertInstanceOf('Firehed\Input\Containers\SafeInput', $this->getSafeInput([]));
    } // testConstruct

    /**
     * @covers ::__construct
     * @expectedException BadMethodCallException
     */
    public function testConstructThrowsWithUnsanitizedInput() {
        $valid = $this->getMockBuilder('Firehed\Input\Containers\SanitizedInput')
            ->disableOriginalConstructor()
            ->getMock();
        $valid->expects($this->atLeastOnce())
            ->method('isValidated')
            ->will($this->returnValue(false));
        new SafeInput($valid);
    } // testConstructThrowsWithUnsanitizedInput

}

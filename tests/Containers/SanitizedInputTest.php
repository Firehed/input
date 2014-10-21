<?php

namespace Firehed\Input\Containers;

use Firehed\Input\Objects\InputObject;

/**
 * @coversDefaultClass Firehed\Input\Containers\SanitizedInput
 */
class SanitizedInputTest extends \PHPUnit_Framework_TestCase {
    private $required = [];
    private $optional = [];

    // ----(Helpers)------------------------------------------------------------

    private function addRequired($key, InputObject $type) {
        $this->required[$key] = $type;
    } // addRequired

    private function addOptional($key, InputObject $type) {
        $this->optional[$key] = $type;
    } // addOptional

    private function getMockIO($valid, $ret = null) {
        $mock = $this->getMockBuilder('Firehed\Input\Objects\InputObject')
            ->setMethods(['evaluate'])
            ->getMockForAbstractClass();

        if ($valid) {
            $mock->expects($this->atLeastOnce())
                ->method('evaluate')
                ->will($this->returnValue($ret));
        }
        else {
            $mock->expects($this->atLeastOnce())
                ->method('evaluate')
                ->will($this->throwException(new \UnexpectedValueException));
        }
        return $mock;
    } // getMockIO

    private function getParsedInput(array $data, $sanitized = true) {
        $mock = $this->getMockBuilder('Firehed\Input\Containers\ParsedInput')
            ->disableOriginalConstructor()
            ->setMethods(['isSanitized', 'getData'])
            ->getMock();
        $mock->expects($this->atLeastOnce())
            ->method('isSanitized')
            ->will($this->returnValue((bool)$sanitized));
        $mock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($data));
        return $mock;
     } // getParsedInput

    private function getValidation() {
        $validation = $this->getMock('Firehed\Input\Interfaces\ValidationInterface');
        $validation->expects($this->atLeastOnce())
            ->method('getRequiredInputs')
            ->will($this->returnValue($this->required));

        $validation->expects($this->atLeastOnce())
            ->method('getOptionalInputs')
            ->will($this->returnValue($this->optional));

        return $validation;
    } // getValidation

    // ----(Constructor)--------------------------------------------------------
    /**
     * @covers ::__construct
     */
    public function testConstructWorks() {
        $mock = $this->getParsedInput([], true);
        $this->assertInstanceOf('Firehed\Input\Containers\SanitizedInput',
            new SanitizedInput($mock),
            'Construct failed');
    } // testConstructWorks

    /**
     * @covers ::__construct
     * @expectedException BadMethodCallException
     */
    public function testConstructThrowsWithUnsanitizedInput() {
        $mock = $this->getParsedInput([], false);
        new SanitizedInput($mock);
    } // testConstructThrowsWithUnparsedInput

    // ----(Unexpected parameters)----------------------------------------------

    /**
     * @covers ::validate
     * @expectedException Firehed\Input\Exceptions\InputException
     * @expectedExceptionCode Firehed\Input\Exceptions\InputException::UNEXPECTED_VALUES
     */
    public function testUnexpectedParametersAreCaught() {
        $parsed = new SanitizedInput($this->getParsedInput(['foo' => 'bar']));
        $parsed->validate($this->getValidation());
    } // testUnexpectedParametersAreCaught

    // ----(Required parameters)-----------------------------------------------

    /**
     * @covers ::validate
     */
    public function testValidRequiredParametersAreReturned() {
        $desc = 'I am a short description';
        $this->addRequired('short', $this->getMockIO(true, $desc));

        $parsed = new SanitizedInput($this->getParsedInput(['short' => $desc]));
        $ret = $parsed->validate($this->getValidation());

        $this->assertInstanceOf('Firehed\Input\Containers\SafeInput', $ret);
        $this->assertSame($desc, $ret['short'],
            'The wrong value exists in the parsed input');
    } // testValidRequiredParametersAreReturned

    /**
     * @covers ::validate
     * @expectedException Firehed\Input\Exceptions\InputException
     * @expectedExceptionCode Firehed\Input\Exceptions\InputException::INVALID_VALUES
     */
    public function testInvalidRequiredParametersAreCaught() {
        $this->addRequired('short', $this->getMockIO(false));

        $parsed = new SanitizedInput($this->getParsedInput(['short' => 123]));
        $parsed->validate($this->getValidation());
    } // testInvalidRequiredParametersAreCaught

    /**
     * @covers ::validate
     * @expectedException Firehed\Input\Exceptions\InputException
     * @expectedExceptionCode Firehed\Input\Exceptions\InputException::MISSING_VALUES
     */
    public function testMissingRequiredParametersAreCaught() {
        $this->addRequired('short',
            $this->getMockForAbstractClass('Firehed\Input\Objects\InputObject'));

        $parsed = new SanitizedInput($this->getParsedInput([]));
        $parsed->validate($this->getValidation());
    } // testMissingRequiredParametersAreCaught

    // ----(Optional parameters)-----------------------------------------------

    /**
     * @covers ::validate
     */
    public function testValidOptionalParametersAreReturned() {
        $desc = 'I am a short description';
        $this->addOptional('short', $this->getMockIO(true, $desc));

        $parsed = new SanitizedInput($this->getParsedInput(['short' => $desc]));
        $ret = $parsed->validate($this->getValidation());

        $this->assertInstanceOf('Firehed\Input\Containers\SafeInput', $ret);
        $this->assertSame($desc, $ret['short'],
            'The wrong value exists in the parsed input');
    } // testValidOptionalParametersAreReturned

    /**
     * @covers ::validate
     * @expectedException Firehed\Input\Exceptions\InputException
     * @expectedExceptionCode Firehed\Input\Exceptions\InputException::INVALID_VALUES
     */
    public function testInvalidOptionalParametersAreCaught() {
        $this->addOptional('short', $this->getMockIO(false));
        $parsed = new SanitizedInput($this->getParsedInput(['short' => 123]));
        $parsed->validate($this->getValidation());
    } // testInvalidOptionalParametersAreCaught

    /**
     * @covers ::validate
     */
    public function testMissingOptionalParametersAreSetToNull() {
        $this->addOptional('short',
            $this->getMockForAbstractClass('Firehed\Input\Objects\InputObject'));

        $parsed = new SanitizedInput($this->getParsedInput([]));
        $ret = $parsed->validate($this->getValidation());
        $this->assertInstanceOf('Firehed\Input\Containers\SafeInput', $ret);
        $this->assertNull($ret['short'],
            "'short' should have defaulted to null");
    } // testMissingOptionalParametersAreSetToNull




}

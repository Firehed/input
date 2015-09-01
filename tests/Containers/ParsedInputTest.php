<?php

namespace Firehed\Input\Containers;

use Firehed\Input\Objects\InputObject;

/**
 * @coversDefaultClass Firehed\Input\Containers\ParsedInput
 */
class ParsedInputTest extends \PHPUnit_Framework_TestCase {

    // ----(Constructor)--------------------------------------------------------

    /**
     * @covers ::__construct
     */
    public function testConstructWorks() {
        $this->assertInstanceOf('Firehed\Input\Containers\ParsedInput',
            new ParsedInput([]),
            'Construct failed');
    } // testConstructWorks

    // ----(ArrayAccess)--------------------------------------------------------

    /**
     * @covers ::offsetGet
     */
    public function testGoodOffset() {
        $array = ['foo' => 'bar'];
        $obj = new ParsedInput($array);
        $this->assertSame('bar',
            $obj['foo'],
            'The value of the parsed input was not the same as the input');
    } // testGoodOffset

    /**
     * @covers ::offsetGet
     * @expectedException DomainException
     */
    public function testBadOffset() {
        $obj = new ParsedInput([]);
        $data = $obj['foo'];
    } // testBadOffset

    /**
     * @covers ::offsetGet
     */
    public function testAccessOfExpectedNullValue() {
        // isset() returns false on null, so assert there's no weirdness
        $obj = new ParsedInput(['foo' => null]);
        $this->assertNull($obj['foo'], 'The wrong value was returned');
    } // testAccessOfExpectedNullValue


    /**
     * @covers ::offsetExists
     * @expectedException BadMethodCallException
     */
    public function testIssetThrows() {
        $obj = new ParsedInput([]);
        isset($obj['foo']);
    } // testIssetThrows

    /**
     * @covers ::offsetExists
     * @expectedException BadMethodCallException
     */
    public function testEmptyThrows() {
        $obj = new ParsedInput([]);
        empty($obj['foo']);
    } // testEmptyThrows

    /**
     * @covers ::offsetUnset
     * @expectedException BadMethodCallException
     */
    public function testUnsetThrows() {
        $obj = new ParsedInput([]);
        unset($obj['foo']);
    } // testUnsetThrows

    /**
     * @covers ::offsetSet
     * @expectedException BadMethodCallException
     */
    public function testSetThrows() {
        $obj = new ParsedInput([]);
        $obj['foo'] = 'bar';
    } // testSetThrows

    // ----(Validation:Unexpected Parameters)----------------------------------
    /**
     * @covers ::validate
     * @expectedException Firehed\Input\Exceptions\InputException
     * @expectedExceptionCode Firehed\Input\Exceptions\InputException::UNEXPECTED_VALUES
     */
    public function testUnexpectedParametersAreCaught() {
        $parsed = new ParsedInput(['foo' => 'bar']);
        $parsed->validate($this->getValidation());
    } // testUnexpectedParametersAreCaught

    // ----(Validation:Required Parameters)------------------------------------
    /**
     * @covers ::validate
     */
    public function testValidRequiredParametersAreReturned() {
        $desc = 'I am a short description';
        $this->addRequired('short', $this->getMockIO(true, $desc));

        $parsed = new ParsedInput(['short' => $desc]);
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

        $parsed = new ParsedInput(['short' => 123]);
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

        $parsed = new ParsedInput([]);
        $parsed->validate($this->getValidation());
    } // testMissingRequiredParametersAreCaught




     // ----(Validation:Optional Parameters)------------------------------------

    /**
     * @covers ::validate
     */
    public function testValidOptionalParametersAreReturned() {
        $desc = 'I am a short description';
        $this->addOptional('short', $this->getMockIO(true, $desc));

        $parsed = new ParsedInput(['short' => $desc]);
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
        $parsed = new ParsedInput(['short' => 123]);
        $parsed->validate($this->getValidation());
    } // testInvalidOptionalParametersAreCaught

    /**
     * @covers ::validate
     */
    public function testMissingOptionalParametersAreSetToNull() {
        $this->addOptional('short',
            $this->getMockForAbstractClass('Firehed\Input\Objects\InputObject'));

        $parsed = new ParsedInput([]);
        $ret = $parsed->validate($this->getValidation());
        $this->assertInstanceOf('Firehed\Input\Containers\SafeInput', $ret);
        $this->assertNull($ret['short'],
            "'short' should have defaulted to null");
    } // testMissingOptionalParametersAreSetToNull




    // ----(Helpers)-----------------------------------------------------------

    private $required = [];
    private $optional = [];

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



}

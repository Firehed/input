<?php

namespace Firehed\Input\Containers;

use Firehed\Input\Exceptions\InputException;
use Firehed\Input\Objects\InputObject;

/**
 * @coversDefaultClass Firehed\Input\Containers\ParsedInput
 */
class ParsedInputTest extends \PHPUnit\Framework\TestCase {

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


    // ----(Validation:Nesting)-------------------------------------------------

    /**
     * @covers ::validate
     * @dataProvider nestedValidationExceptions
     */
    public function testValidateHandlesInputExceptions(
        InputException $ex,
        array $invalid,
        array $missing,
        array $unexpected,
        bool $required
    ) {
        $io = $this->createMock(InputObject::class);
        $io->expects($this->atLeastOnce())
            ->method('evaluate')
            ->will($this->throwException($ex));
        if ($required) {
            $this->addRequired('struct', $io);
            $msg = 'Required:';
        } else {
            $this->addOptional('struct', $io);
            $msg = 'Optional:';
        }
        $parsed = new ParsedInput(['struct' => ['a' => 1, 'b' => 2]]);
        try {
            $ret = $parsed->validate($this->getValidation());
            $this->fail('An inputException should have been thrown');
        } catch (InputException $e) {
            $this->assertSame($invalid, $e->getInvalid(), "$msg Invalid was wrong");
            $this->assertSame($missing, $e->getMissing(), "$msg Missing was wrong");
            $this->assertSame($unexpected, $e->getUnexpected(), "$msg Unexpected was wrong");
        }
    }


    public function nestedValidationExceptions()
    {

        return [
            // Required inputs
            [
                new InputException(InputException::INVALID_VALUES, ['a']),
                ['struct.a'],
                [],
                [],
                true,
            ],
            [
                new InputException(InputException::MISSING_VALUES, ['c']),
                [],
                ['struct.c'],
                [],
                true,
            ],
            [
                new InputException(InputException::MISSING_VALUES, ['c', 'd']),
                [],
                ['struct.c', 'struct.d'],
                [],
                true,
            ],
            [
                new InputException(InputException::UNEXPECTED_VALUES, ['b']),
                [],
                [],
                ['struct.b'],
                true,
            ],
            [
                new InputException(InputException::MULTIPLE_VALUE_ERRORS, [
                    'invalid' => ['a'],
                    'missing' => ['c'],
                    'unexpected' => ['b'],
                ]),
                ['struct.a'],
                ['struct.c'],
                ['struct.b'],
                true,
            ],
            // Optional inputs
            [
                new InputException(InputException::INVALID_VALUES, ['a']),
                ['struct.a'],
                [],
                [],
                false,
            ],
            [
                new InputException(InputException::MISSING_VALUES, ['c']),
                [],
                ['struct.c'],
                [],
                false,
            ],
            [
                new InputException(InputException::MISSING_VALUES, ['c', 'd']),
                [],
                ['struct.c', 'struct.d'],
                [],
                false,
            ],
            [
                new InputException(InputException::UNEXPECTED_VALUES, ['b']),
                [],
                [],
                ['struct.b'],
                false,
            ],
            [
                new InputException(InputException::MULTIPLE_VALUE_ERRORS, [
                    'invalid' => ['a'],
                    'missing' => ['c'],
                    'unexpected' => ['b'],
                ]),
                ['struct.a'],
                ['struct.c'],
                ['struct.b'],
                false,
            ],
        ];
    }

    // ----(Helpers)-----------------------------------------------------------

    private $required = [];
    private $optional = [];

    private function getValidation() {
        $validation = $this->createMock('Firehed\Input\Interfaces\ValidationInterface');
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

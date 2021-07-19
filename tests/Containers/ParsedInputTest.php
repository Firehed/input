<?php

namespace Firehed\Input\Containers;

use BadMethodCallException;
use DomainException;
use Firehed\Input\Exceptions\InputException;
use Firehed\Input\Objects\InputObject;
use UnexpectedValueException;

/**
 * @coversDefaultClass Firehed\Input\Containers\ParsedInput
 */
class ParsedInputTest extends \PHPUnit\Framework\TestCase
{

    // ----(Constructor)--------------------------------------------------------

    /**
     * @covers ::__construct
     */
    public function testConstructWorks(): void
    {
        $this->assertInstanceOf(
            'Firehed\Input\Containers\ParsedInput',
            new ParsedInput([]),
            'Construct failed'
        );
    }

    // ----(ArrayAccess)--------------------------------------------------------

    /**
     * @covers ::offsetGet
     */
    public function testGoodOffset(): void
    {
        $array = ['foo' => 'bar'];
        $obj = new ParsedInput($array);
        $this->assertSame(
            'bar',
            $obj['foo'],
            'The value of the parsed input was not the same as the input'
        );
    }

    /**
     * @covers ::offsetGet
     */
    public function testBadOffset(): void
    {
        $obj = new ParsedInput([]);
        $this->expectException(DomainException::class);
        $data = $obj['foo'];
    }

    /**
     * @covers ::offsetGet
     */
    public function testAccessOfExpectedNullValue(): void
    {
        // isset() returns false on null, so assert there's no weirdness
        $obj = new ParsedInput(['foo' => null]);
        $this->assertNull($obj['foo'], 'The wrong value was returned');
    }


    /**
     * @covers ::offsetExists
     */
    public function testIssetThrows(): void
    {
        $obj = new ParsedInput([]);
        $this->expectException(BadMethodCallException::class);
        isset($obj['foo']);
    }

    /**
     * @covers ::offsetExists
     */
    public function testEmptyThrows(): void
    {
        $obj = new ParsedInput([]);
        $this->expectException(BadMethodCallException::class);
        empty($obj['foo']);
    }

    /**
     * @covers ::offsetUnset
     */
    public function testUnsetThrows(): void
    {
        $obj = new ParsedInput([]);
        $this->expectException(BadMethodCallException::class);
        unset($obj['foo']);
    }

    /**
     * @covers ::offsetSet
     */
    public function testSetThrows(): void
    {
        $obj = new ParsedInput([]);
        $this->expectException(BadMethodCallException::class);
        $obj['foo'] = 'bar';
    }

    // ----(Validation:Unexpected Parameters)----------------------------------
    /**
     * @covers ::validate
     */
    public function testUnexpectedParametersAreCaught(): void
    {
        $parsed = new ParsedInput(['foo' => 'bar']);
        $this->expectException(InputException::class);
        $this->expectExceptionCode(InputException::UNEXPECTED_VALUES);
        $parsed->validate($this->getValidation());
    }

    // ----(Validation:Required Parameters)------------------------------------
    /**
     * @covers ::validate
     */
    public function testValidRequiredParametersAreReturned(): void
    {
        $desc = 'I am a short description';
        $this->addRequired('short', $this->getMockIO(true, $desc));

        $parsed = new ParsedInput(['short' => $desc]);
        $ret = $parsed->validate($this->getValidation());

        $this->assertInstanceOf('Firehed\Input\Containers\SafeInput', $ret);
        $this->assertSame(
            $desc,
            $ret['short'],
            'The wrong value exists in the parsed input'
        );
    }

   /**
     * @covers ::validate
     */
    public function testInvalidRequiredParametersAreCaught(): void
    {
        $this->addRequired('short', $this->getMockIO(false));

        $parsed = new ParsedInput(['short' => 123]);
        $this->expectException(InputException::class);
        $this->expectExceptionCode(InputException::INVALID_VALUES);
        $parsed->validate($this->getValidation());
    }

    /**
     * @covers ::validate
     */
    public function testMissingRequiredParametersAreCaught(): void
    {
        $this->addRequired(
            'short',
            $this->getMockForAbstractClass('Firehed\Input\Objects\InputObject')
        );

        $parsed = new ParsedInput([]);
        $this->expectException(InputException::class);
        $this->expectExceptionCode(InputException::MISSING_VALUES);
        $parsed->validate($this->getValidation());
    }

    /**
     * @covers ::validate
     */
    public function testRequiredParameterWithDefaultDoesntUseDefault(): void
    {
        $default = 'some default value';
        $io = $this->createMock(InputObject::class);
        $io->expects($this->never())
            ->method('getDefaultValue');
        $this->addRequired('short', $io);
        $parsed = new ParsedInput([]);
        $this->expectException(InputException::class);
        $this->expectExceptionCode(InputException::MISSING_VALUES);
        $parsed->validate($this->getValidation());
    }

     // ----(Validation:Optional Parameters)------------------------------------

    /**
     * @covers ::validate
     */
    public function testValidOptionalParametersAreReturned(): void
    {
        $desc = 'I am a short description';
        $this->addOptional('short', $this->getMockIO(true, $desc));

        $parsed = new ParsedInput(['short' => $desc]);
        $ret = $parsed->validate($this->getValidation());

        $this->assertInstanceOf('Firehed\Input\Containers\SafeInput', $ret);
        $this->assertSame(
            $desc,
            $ret['short'],
            'The wrong value exists in the parsed input'
        );
    }

    /**
     * @covers ::validate
     */
    public function testInvalidOptionalParametersAreCaught(): void
    {
        $this->addOptional('short', $this->getMockIO(false));
        $parsed = new ParsedInput(['short' => 123]);
        $this->expectException(InputException::class);
        $this->expectExceptionCode(InputException::INVALID_VALUES);
        $parsed->validate($this->getValidation());
    }

    /**
     * @covers ::validate
     */
    public function testMissingOptionalParametersAreSetToNull(): void
    {
        $this->addOptional(
            'short',
            $this->getMockForAbstractClass('Firehed\Input\Objects\InputObject')
        );

        $parsed = new ParsedInput([]);
        $ret = $parsed->validate($this->getValidation());
        $this->assertInstanceOf('Firehed\Input\Containers\SafeInput', $ret);
        $this->assertNull(
            $ret['short'],
            "'short' should have defaulted to null"
        );
    }

    /**
     * @covers ::validate
     */
    public function testOptionalParametersWithDefaultsUseDefaults(): void
    {
        $default = 'some default value';
        $io = $this->createMock(InputObject::class);
        $io->expects($this->atLeastOnce())
            ->method('getDefaultValue')
            ->willReturn($default);
        $this->addOptional('short', $io);
        $parsed = new ParsedInput([]);
        $ret = $parsed->validate($this->getValidation());
        $this->assertInstanceOf(SafeInput::class, $ret);
        $this->assertSame($default, $ret['short'], "'short' did not yield its default value");
    }

    /**
     * @covers ::validate
     */
    public function testOptionalParametersWithValidNullWorksWhenProvided(): void
    {
        $io = $this->getMockIO(true, null);
        $io->expects($this->never())
            ->method('getDefaultValue');
        $this->addOptional('key', $io);
        $parsed = new ParsedInput(['key' => null]);
        $ret = $parsed->validate($this->getValidation());
        $this->assertInstanceOf(SafeInput::class, $ret);
        $this->assertNull($ret['key'], 'key should have been null literal');
    }

    /**
     * @covers ::validate
     */
    public function testOptionalParametersWithValidNullWorksWhenNotProvided(): void
    {
        $io = $this->createMock(InputObject::class);
        $io->expects($this->atLeastOnce())
            ->method('getDefaultValue')
            ->willReturn(false);
        $this->addOptional('key', $io);
        $parsed = new ParsedInput([]);
        $ret = $parsed->validate($this->getValidation());
        $this->assertInstanceOf(SafeInput::class, $ret);
        $this->assertFalse($ret['key'], 'key should have been false literal');
    }

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
    ): void {
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

    private function getValidation()
    {
        $validation = $this->createMock('Firehed\Input\Interfaces\ValidationInterface');
        $validation->expects($this->atLeastOnce())
            ->method('getRequiredInputs')
            ->will($this->returnValue($this->required));

        $validation->expects($this->atLeastOnce())
            ->method('getOptionalInputs')
            ->will($this->returnValue($this->optional));

        return $validation;
    }

    private function addRequired(string $key, InputObject $type)
    {
        $this->required[$key] = $type;
    }

    private function addOptional(string $key, InputObject $type)
    {
        $this->optional[$key] = $type;
    }

    private function getMockIO(bool $valid, $ret = null)
    {
        $mock = $this->getMockBuilder(InputObject::class)
            ->setMethods(['evaluate', 'getDefaultValue'])
            ->getMockForAbstractClass();

        if ($valid) {
            $mock->expects($this->atLeastOnce())
                ->method('evaluate')
                ->will($this->returnValue($ret));
        } else {
            $mock->expects($this->atLeastOnce())
                ->method('evaluate')
                ->will($this->throwException(new UnexpectedValueException()));
        }
        return $mock;
    }
}

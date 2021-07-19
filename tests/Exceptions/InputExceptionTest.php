<?php

declare(strict_types=1);

namespace Firehed\Input\Exceptions;

use LogicException;
use TypeError;

/**
 * @coversDefaultClass Firehed\Input\Exceptions\InputException
 */
class InputExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function constants()
    {
        $rc = new \ReflectionClass('Firehed\Input\Exceptions\InputException');
        $constants = $rc->getConstants();
        $out = [];
        foreach ($constants as $k => $v) {
            $out[] = [$v, $k];
        }
        return $out;
    }

    /**
     * @covers ::__construct
     * @dataProvider constants
     */
    public function testConstruct($constant, $name): void
    {
        $this->assertInstanceOf(
            'Firehed\Input\Exceptions\InputException',
            new InputException($constant),
            sprintf("%s was not handled", $name)
        );
    }

    /**
     * @covers ::__construct
     */
    public function testInvalidConstructWithInt(): void
    {
        try {
            new InputException(999999);
        } catch (LogicException $e) {
            $this->assertTrue(true, 'test passed');
        }
    }

    /**
     * @covers ::getInvalid
     * @dataProvider constants
     */
    public function testGetInvalid(int $constant): void
    {
        $ex = new InputException($constant, ['foo']);
        if ($constant === InputException::INVALID_VALUES) {
            $this->assertSame(['foo'], $ex->getInvalid());
        } else {
            $this->assertSame([], $ex->getInvalid());
        }
    }

    /**
     * @covers ::getMissing
     * @dataProvider constants
     */
    public function testGetMissing(int $constant): void
    {
        $ex = new InputException($constant, ['foo']);
        if ($constant === InputException::MISSING_VALUES) {
            $this->assertSame(['foo'], $ex->getMissing());
        } else {
            $this->assertSame([], $ex->getMissing());
        }
    }

    /**
     * @covers ::getUnexpected
     * @dataProvider constants
     */
    public function testGetUnexpected(int $constant): void
    {
        $ex = new InputException($constant, ['foo']);
        if ($constant === InputException::UNEXPECTED_VALUES) {
            $this->assertSame(['foo'], $ex->getUnexpected());
        } else {
            $this->assertSame([], $ex->getUnexpected());
        }
    }

    /**
     * @covers ::getInvalid
     * @covers ::getMissing
     * @covers ::getUnexpected
     */
    public function testMultipleErrors(): void
    {
        $errors = [
            'invalid' => ['invalid_key'],
            'missing' => ['missing_key'],
            'unexpected' => ['unexpected_key'],
        ];
        $ex = new InputException(InputException::MULTIPLE_VALUE_ERRORS, $errors);
        $this->assertSame($errors['invalid'], $ex->getInvalid(), 'Invalid wrong');
        $this->assertSame($errors['missing'], $ex->getMissing(), 'Missing wrong');
        $this->assertSame($errors['unexpected'], $ex->getUnexpected(), 'Unexpected wrong');
    }
}

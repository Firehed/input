<?php

declare(strict_types=1);

namespace Firehed\Input\Exceptions;

use LogicException;
use ReflectionClass;
use TypeError;

/**
 * @covers Firehed\Input\Exceptions\InputException
 */
class InputExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array{int, string}[]
     */
    public function constants(): array
    {
        $rc = new ReflectionClass(InputException::class);
        $constants = $rc->getConstants();
        $out = [];
        foreach ($constants as $k => $v) {
            assert(is_int($v));
            $out[] = [$v, $k];
        }
        return $out;
    }

    /**
     * @dataProvider constants
     */
    public function testConstruct(int $constant, string $name): void
    {
        $this->assertInstanceOf(
            InputException::class,
            new InputException($constant),
            sprintf("%s was not handled", $name)
        );
    }

    public function testInvalidConstructWithInt(): void
    {
        try {
            new InputException(999999);
        } catch (LogicException $e) {
            $this->assertTrue(true, 'test passed');
        }
    }

    /**
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

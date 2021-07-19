<?php

declare(strict_types=1);

namespace Firehed\Input\Objects;

use BadMethodCallException;
use UnexpectedValueException;

/**
 * @coversDefaultClass Firehed\Input\Objects\InputObject
 * @covers ::<protected>
 * @covers ::<private>
 */
class InputObjectTest extends \PHPUnit\Framework\TestCase
{

    private $io;
    public function setUp(): void
    {
        $this->io = new InputObjectTestFixture();
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(InputObject::class, $this->io);
    }

    /** @covers ::setValue */
    public function testSetValue()
    {
        $ret = $this->io->setValue(null);
        $this->assertSame(
            $this->io,
            $ret,
            'InputObject::setValue should return $this'
        );
        // should not throw
    }

    /** @covers ::isValid */
    public function testIsValidGood()
    {
        $this->io->setValue(false);
        $this->assertTrue($this->io->isValid());
    }

    /** @covers ::isValid */
    public function testIsValidBad()
    {
        $this->io->setValue(InputObjectTestFixture::MAGIC_FAIL);
        $this->assertFalse($this->io->isValid());
    }

    /** @covers ::isValid */
    public function testIsValidNoValueThrows()
    {
        $this->expectException(BadMethodCallException::class);
        $this->io->isValid();
    }

    /** @covers ::evaluate */
    public function testEvaluateValidValue()
    {
        $dummy = '1290ajkflk alskdjf 19 ';
        $this->io->setValue($dummy);
        $this->assertSame(
            $dummy,
            $this->io->evaluate(),
            "evaluate should default to returning the original value"
        );
    }

    /** @covers ::evaluate */
    public function testEvaluateInvalidValue()
    {
        $this->io->setValue(InputObjectTestFixture::MAGIC_FAIL);
        $this->expectException(UnexpectedValueException::class);
        $this->io->evaluate();
    }

    /** @covers ::evaluate */
    public function testEvaluateNoValue()
    {
        $this->expectException(BadMethodCallException::class);
        $this->io->evaluate();
    }

    /** @covers ::getDefaultValue */
    public function testDefaultDefaultValueIsNull()
    {
        $this->assertNull($this->io->getDefaultValue());
    }

    /**
     * @covers ::getDefaultValue
     * @covers ::setDefaultValue
     */
    public function testSetDefaultValueWorksAndReturnsSelf()
    {
        $default = 'some default';
        $this->assertNull($this->io->getDefaultValue());
        $this->assertSame($this->io, $this->io->setDefaultValue($default));
        $this->assertSame($default, $this->io->getDefaultValue());
    }
}

<?php

namespace Firehed\Input\Objects;

/**
 * @coversDefaultClass Firehed\Input\Objects\InputObject
 * @covers ::<protected>
 * @covers ::<private>
 */
class InputObjectTest extends \PHPUnit_Framework_TestCase {

    private $io;
    public function setUp() {
        $this->io = new InputObjectTestFixture;
    }

    /** @covers ::setValue */
    public function testSetValue() {
        $ret = $this->io->setValue(null);
        $this->assertSame($this->io, $ret,
            'InputObject::setValue should return $this');
        // should not throw
    }

    /** @covers ::isValid */
    public function testIsValidGood() {
        $this->io->setValue(false);
        $this->assertTrue($this->io->isValid());
    }

    /** @covers ::isValid */
    public function testIsValidBad() {
        $this->io->setValue(InputObjectTestFixture::MAGIC_FAIL);
        $this->assertFalse($this->io->isValid());
    }

    /** @covers ::isValid */
    public function testIsValidNoValueThrows() {
        try {
            $this->io->isValid();
            $this->fail("isValid should have thrown an exception");
        } catch (\BadMethodCallException $e) {
            return;
        }
    }

    /** @covers ::evaluate */
    public function testEvaluateValidValue() {
        $dummy = '1290ajkflk alskdjf 19 ';
        $this->io->setValue($dummy);
        $this->assertSame($dummy, $this->io->evaluate(),
            "evaluate should default to returning the original value");
    }

    /** @covers ::evaluate */
    public function testEvaluateInvalidValue() {
        $this->io->setValue(InputObjectTestFixture::MAGIC_FAIL);
        try {
            $this->io->evaluate();
            $this->fail("evaluate should have thrown an exception");
        }
        catch (\UnexpectedValueException $e) {
            return;
        }
    }

    /** @covers ::evaluate */
    public function testEvaluateNoValue() {
        try {
            $this->io->evaluate();
            $this->fail("evaluate should have thrown an exception");
        } catch (\BadMethodCallException $e) {
            return;
        }
    }
}

class InputObjectTestFixture extends InputObject {

    const MAGIC_FAIL = '198sjs $ a2/';
    public function validate($value) {
        return $value !== self::MAGIC_FAIL;
    } // validate
}

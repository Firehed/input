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

    /** @covers ::addDependency */
    public function testAddDependencyReturnsThis() {
        $this->assertSame($this->io,
            $this->io->addDependency('key', new InputObjectTestFixture()),
            'addDependency did not return $this');
    } // testAddDependencyReturnsThis

    /** @covers ::hasUnresolvedDependencies */
    public function testDependencyResolutionBeforeValueIsSet() {
        $dep = new InputObjectTestFixture();
        $this->io->addDependency('key', $dep);
        $this->assertTrue($this->io->hasUnresolvedDependencies(),
            'The dependency had no value set and should be considered unresolved');
        return [$this->io, $dep];
    } // testDependencyResolutionBeforeValueIsSet

    /**
     * @covers ::hasUnresolvedDependencies
     * @depends testDependencyResolutionBeforeValueIsSet
     */
    public function testDependencyResolutionOutOfOrder(array $deps) {
        list($this->io, $dep) = $deps;
        $this->assertTrue($this->io->hasUnresolvedDependencies());
        $dep->setValue('this is valid');
        $this->assertTrue($dep->isValid()); // Sanity check
        $this->assertFalse($this->io->hasUnresolvedDependencies(),
            'All values are valid, there should be no unresolved dependencies');
    } // testDependencyResolutionOutOfOrder

    /** @covers ::hasUnresolvedDependencies */
    public function testMultipleDependencyResolution() {
        $dep1 = new InputObjectTestFixture();
        $dep2 = new InputObjectTestFixture();
        $this->io->addDependency('key1', $dep1)
            ->addDependency('key2', $dep2);

        $this->assertTrue($this->io->hasUnresolvedDependencies(),
            'Sanity check of unresolved dependencies failed');

        $dep1->setValue('this is valid');
        $this->assertTrue($dep1->isValid(),
            'Good dependency sanity check failed');
        try {
            $this->assertFalse($dep2->isValid(),
                'Bad dependency sanity check failed');
            $this->fail('isValid should have thrown');
        } catch (\BadMethodCallException $e) {} // all systems normal
        $this->assertTrue($this->io->hasUnresolvedDependencies(),
            'One unset dependency should be considered unresolved');

        $dep2->setValue(InputObjectTestFixture::MAGIC_FAIL);
        $this->assertFalse($dep2->isValid(),
            'Bad dependency sanity check failed');
        $this->assertTrue($this->io->hasUnresolvedDependencies(),
            'One invalid dependency should be considered unresolved');

        $dep2->setValue('this is also valid');
        $this->assertTrue($dep2->isValid(),
            'Second good sanity check failed');
        $this->assertFalse($this->io->hasUnresolvedDependencies(),
            'All dependencies should now be good');
    } // testAnyBadDependencyWillCauseUnresolved

    /** @covers ::hasUnresolvedDependencies */
    public function testDependencyResolutionWithInvalidValue() {
        $dep = new InputObjectTestFixture();
        $dep->setValue(InputObjectTestFixture::MAGIC_FAIL);
        $this->io->addDependency('key', $dep);
        $this->assertTrue($this->io->hasUnresolvedDependencies(),
            'The dependency was invalid and should be considerd unresolved');
    }  // testDependencyResolutionWithInvalidValue

}

class InputObjectTestFixture extends InputObject {

    const MAGIC_FAIL = '198sjs $ a2/';
    public function validate($value) {
        return $value !== self::MAGIC_FAIL;
    } // validate
}

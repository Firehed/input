<?php

namespace Firehed\Input\Objects;

/**
 * @coversDefaultClass Firehed\Input\Objects\Enum
 * @covers ::<protected>
 * @covers ::<private>
 */
class EnumTest extends \PHPUnit_Framework_TestCase {

    public function testValidValue() {
        $fixture = new EnumTestFixture();
        $fixture->setValue('hello');
        $this->assertTrue($fixture->isValid(), 'Should have been valid');
        $this->assertSame('hello', $fixture->evaluate(),
            'The wrong value was returned from evaluate');
    } // testValidValue

    public function testInvalidValue() {
        $fixture = new EnumTestFixture();
        $fixture->setValue('hola');
        $this->assertFalse($fixture->isValid(), 'Should have been invalid');
    } // testInvalidValues

}

class EnumTestFixture extends Enum {

    protected function getValidValues() {
        return [
            'hello',
            'goodbye',
        ];
    } // getValidValues

}

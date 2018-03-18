<?php

namespace Firehed\Input\Exceptions;

/**
 * @coversDefaultClass Firehed\Input\Exceptions\InputException
 */
class InputExceptionTest extends \PHPUnit\Framework\TestCase {

    public function constants() {
        $rc = new \ReflectionClass('Firehed\Input\Exceptions\InputException');
        $constants = $rc->getConstants();
        $out = [];
        foreach ($constants as $k => $v) {
            $out[] = [$v, $k];
        }
        return $out;
    } // constants

    /**
     * @covers ::__construct
     * @dataProvider constants
     * */
    public function testConstruct($constant, $name) {
        $this->assertInstanceOf('Firehed\Input\Exceptions\InputException',
            new InputException($constant),
            sprintf("%s was not handled", $name));
    } // testConstruct

    /**
     * @covers ::__construct
     * @expectedException LogicException
     */
    public function testInvalidConstruct() {
        new InputException('this is not a defined value');
    } // testInvalidConstruct
}

<?php

namespace Firehed\Input\Objects;

/**
 * @coversDefaultClass Firehed\Input\Objects\Structure
 * @covers ::<protected>
 * @covers ::<private>
 * */
class StructureTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::evaluate
     */
    public function testExecuteValidData() {
        $structure = $this->getMockForAbstractClass('Firehed\Input\Objects\Structure');
        $structure->expects($this->atLeastOnce())
            ->method('getRequiredInputs')
            ->will($this->returnValue(['string' => new String]));
        $structure->expects($this->atLeastOnce())
            ->method('getOptionalInputs')
            ->will($this->returnValue([]));
        $ret = $structure->setValue(['string' => 'this is a string'])->evaluate();
        $this->assertSame(['string' => 'this is a string'],
            $ret,
            'Execute should have returned an array');
   }

    /**
     * @covers ::evaluate
     * @expectedException UnexpectedValueException
     */
    public function testExecuteInvalidData() {
        $structure = $this->getMockForAbstractClass('Firehed\Input\Objects\Structure');
        $structure->expects($this->atLeastOnce())
            ->method('getRequiredInputs')
            ->will($this->returnValue(['string' => new String]));
        $structure->expects($this->atLeastOnce())
            ->method('getOptionalInputs')
            ->will($this->returnValue([]));
        $structure->setValue(['string' => 123])->evaluate();
    } // testExecuteInvalidData

    /**
     * @dataProvider nonArrays
     * */
    public function testNonArrayInput($input) {
        $structure = $this->getMockForAbstractClass('Firehed\Input\Objects\Structure');
        $structure->expects($this->any())
            ->method('getRequiredInputs')
            ->will($this->returnValue([]));
        $structure->expects($this->any())
            ->method('getOptionalInputs')
            ->will($this->returnValue([]));
        $structure->setValue($input);
        $this->assertFalse($structure->isValid(),
            'Input should not be valid');
    } // testNonArrayInput
 
    // -(  DataProviders  )-----------------------------------------------------

    public function nonArrays() {
        return [
            [null],
            [true],
            [false],
            [0],
            [1.2],
            ["[]"],
        ];
    } // nonArrays 

}

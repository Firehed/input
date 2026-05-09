<?php

namespace Firehed\Input;

trait SafeInputTestTrait
{
    /**
     * Transform an array of data to a SafeInput object. Intended for testing
     * consumers of validated input where testing the actual validation process
     * is either unnecessary or undesirable.
     *
     * Depends on PHPUnit >=4.8
     *
     * @param array "valid" data
     * @return Containers\SafeInput wrapped valid data
     */
    protected function getSafeInputFromData(array $data)
    {
        $PI = $this->getMockBuilder('Firehed\Input\Containers\ParsedInput')
            ->setConstructorArgs([$data])
            ->getMock();
        $PI->expects($this->any())
            ->method('isValidated')
            ->will($this->returnValue(true));
        return new Containers\SafeInput($PI);
    }
}

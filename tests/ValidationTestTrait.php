<?php

namespace Firehed\Input;

/**
 * Default test cases to be run against any object implementing
 * ValidationInterface. This amounts to glorified type-hinting, but still
 * provides valuable automated coverage that would be otherwise only available
 * at runtime
 */
trait ValidationTestTrait
{
    /**
     * Get the validation class under test
     * @return Interfaces\ValidationInterface
     */
    abstract protected function getValidation();

    /** @covers ::getRequiredInputs */
    public function testGetRequiredInputs()
    {
        $inputs = $this->getValidation()->getRequiredInputs();
        $this->assertInternalType('array',
            $inputs,
            'getRequiredInputs did not return an array');
        foreach ($inputs as $key => $type) {
            $this->assertInternalType('string',
                $key,
                'getRequiredInputs contains an invalid key');
            $this->assertInstanceOf('Firehed\Input\Objects\InputObject',
                $type,
                "getRequiredInputs[$key] is not an InputObject");
        }
    }

    /** @covers ::getOptionalInputs */
    public function testGetOptionalInputs()
    {
        $inputs = $this->getValidation()->getOptionalInputs();
        $this->assertInternalType('array',
            $inputs,
            'getOptionalInputs did not return an array');
        foreach ($inputs as $key => $type) {
            $this->assertInternalType('string',
                $key,
                'getOptionalInputs contains an invalid key');
            $this->assertInstanceOf('Firehed\Input\Objects\InputObject',
                $type,
                "getOptionalInputs[$key] is not an InputObject");
        }
    }

}

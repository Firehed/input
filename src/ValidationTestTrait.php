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

    public function testGetRequiredInputs()
    {
        $inputs = $this->getValidation()->getRequiredInputs();
        $this->assertIsArray(
            $inputs,
            'getRequiredInputs did not return an array'
        );
        foreach ($inputs as $key => $type) {
            $this->assertIsString(
                $key,
                'getRequiredInputs contains an invalid key'
            );
            $this->assertInstanceOf(
                Objects\InputObject::class,
                $type,
                "getRequiredInputs[$key] is not an InputObject"
            );
        }
    }

    public function testGetOptionalInputs()
    {
        $inputs = $this->getValidation()->getOptionalInputs();
        $this->assertIsArray(
            $inputs,
            'getOptionalInputs did not return an array'
        );
        foreach ($inputs as $key => $type) {
            $this->assertIsString(
                $key,
                'getOptionalInputs contains an invalid key'
            );
            $this->assertInstanceOf(
                Objects\InputObject::class,
                $type,
                "getOptionalInputs[$key] is not an InputObject"
            );
        }
    }
}

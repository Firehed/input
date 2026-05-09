<?php

declare(strict_types=1);

namespace Firehed\Input\Containers;

use BadMethodCallException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SafeInput::class)]
class SafeInputTest extends TestCase
{
    /**
     * @param mixed[] $data
     */
    private function getSafeInput(array $data): SafeInput
    {
        $stub = $this->getMockBuilder(ParsedInput::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getData', 'isValidated'])
            ->getMock();
        $stub->method('getData')->willReturn($data);
        $stub->method('isValidated')->willReturn(true);
        return new SafeInput($stub);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testConstruct(): void
    {
        $this->assertInstanceOf(SafeInput::class, $this->getSafeInput([]));
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testConstructThrowsWithUnvalidatedInput(): void
    {
        $stub = $this->getMockBuilder(ParsedInput::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isValidated'])
            ->getMock();
        $stub->method('isValidated')->willReturn(false);
        $this->expectException(BadMethodCallException::class);
        new SafeInput($stub);
    }
}

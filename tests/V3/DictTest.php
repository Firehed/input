<?php

declare(strict_types=1);

namespace Firehed\Input\V3;

/**
 * @covers Firehed\Input\V3\Dict
 */
class DictTest extends \PHPUnit\Framework\TestCase
{
    public function testMissingRequired(): void
    {
        $val = new Dict([
            'r1' => new Any(),
            // '2' => new Email(),
        ]);
        $result = $val->validate([]);
        $result = $result->unwrap();
        \PHPStan\dumpType($result);
        $this->assertFalse($result->isOk());
        $this->markTestIncomplete('error message');
    }

    public function testProvidedRequired(): void
    {
        $val = new Dict([
            'r1' => new Any(),
        ]);
        $result = $val->validate(['r1' => 'value']);
        $this->assertTrue($result->isOk());
        $this->assertSame(['r1' => 'value'], $result->unwrap());
    }

    public function testMissingOptional(): void
    {
        $val = new Dict([
            'r1' => new Optional(new Any()),
        ]);
        $result = $val->validate([]);
        $this->assertTrue($result->isOk());
        $unwrapped = $result->unwrap();
        $this->assertSame(['r1' => null], $unwrapped);
    }

    public function testProvidedOptional(): void
    {
        $val = new Dict([
            'r1' => new Optional(new Any()),
        ]);
        $result = $val->validate(['r1' => 'value']);
        $this->assertTrue($result->isOk());
        $unwrapped = $result->unwrap();
        $this->assertSame(['r1' => 'value'], $unwrapped);
    }

    public function testRecursiveDicts(): void
    {
        $this->markTestSkipped();
    }

    /**
     * @dataProvider validationProvider
     */
    public function testCombinations(Dict $val, array $input, bool $valid): void
    {
        $result = $val->validate($input);
        $this->assertSame($valid, $result->isOk());
    }

    /**
     * @return array{Dict, mixed, bool}[]
     */
    public function validationProvider(): array
    {
        $val = new Dict([
            'r1' => new Any(),
            'o1' => new Optional(new Any()),
            'o2' => new Optional(new Any(), 'Default'),
        ]);
        return [
            [$val, [], false],
            [$val, ['r1' => 'yes'], true],
        ];
    }
}

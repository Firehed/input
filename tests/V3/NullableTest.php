<?php

declare(strict_types=1);

namespace Firehed\Input\V3;

/**
 * @covers Firehed\Input\V3\Nullable
 */
class NullableTest extends \PHPUnit\Framework\TestCase
{
    public function testOkWrapped(): void
    {
        $val = new Nullable(new Email());
        $v = 'example@example.com';
        $result = $val->validate($v);
        $this->assertTrue($result->isOk());
        $this->assertSame($v, $result->unwrap());
    }

    public function testOkNull(): void
    {
        $default = 'default@example.com';
        $val = new Nullable(new Email(), $default);
        $result = $val->validate(null);
        $this->assertTrue($result->isOk());
        $this->assertSame($default, $result->unwrap());
    }
}

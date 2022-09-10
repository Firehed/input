<?php

declare(strict_types=1);

namespace Firehed\Input\V3;

/**
 * @covers Firehed\Input\V3\Email
 */
class EmailTest extends \PHPUnit\Framework\TestCase
{
    public function testOk(): void
    {
        $v = 'example@example.com';
        $val = new Email();
        $result = $val->validate($v);
        $this->assertTrue($result->isOk());
        $this->assertSame($v, $result->unwrap());
    }
}

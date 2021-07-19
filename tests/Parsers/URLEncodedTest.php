<?php

namespace Firehed\Input\Parsers;

use Firehed\Input\Exceptions\InputException;

/**
 * @covers Firehed\Input\Parsers\URLEncoded
 */
class URLEncodedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array{string, mixed}[]
     */
    public function validURLEncoded(): array
    {
        return [
            ['foo=bar', ['foo' => 'bar']],
            ['a=b&c=d', ['a' => 'b', 'c' => 'd']],
            ['a[]=b&a[]=c', ['a' => ['b', 'c']]],
            ['', []], // Cast empty body to empty array
        ];
    }

    /**
     * @return string[][]
     */
    public function formatErrors(): array
    {
        return [
            ['&'],
        ];
    }

    /**
     * @dataProvider validURLEncoded
     * @param mixed $expected
     */
    public function testParse(string $data, $expected): void
    {
        $parser = new URLEncoded();

        $ret = $parser->parse($data);

        $this->assertEquals(
            $expected,
            $ret,
            'Parser returned wrong value from URLEncoded'
        );
    }

    /**
     * @dataProvider formatErrors
     */
    public function testFormatError(string $data): void
    {
        $parser = new URLEncoded();
        $this->expectException(InputException::class);
        $this->expectExceptionCode(InputException::FORMAT_ERROR);
        $parser->parse($data);
    }
}

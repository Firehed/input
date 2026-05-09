<?php

declare(strict_types=1);

namespace Firehed\Input\Parsers;

use Firehed\Input\Exceptions\InputException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(URLEncoded::class)]
class URLEncodedTest extends TestCase
{
    /**
     * @return array{string, mixed}[]
     */
    public static function validURLEncoded(): array
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
    public static function formatErrors(): array
    {
        return [
            ['&'],
        ];
    }

    #[DataProvider('validURLEncoded')]
    public function testParse(string $data, mixed $expected): void
    {
        $parser = new URLEncoded();

        $ret = $parser->parse($data);

        $this->assertEquals(
            $expected,
            $ret,
            'Parser returned wrong value from URLEncoded'
        );
    }

    #[DataProvider('formatErrors')]
    public function testFormatError(string $data): void
    {
        $parser = new URLEncoded();
        $this->expectException(InputException::class);
        $this->expectExceptionCode(InputException::FORMAT_ERROR);
        $parser->parse($data);
    }
}

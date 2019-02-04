<?php

namespace Firehed\Input\Parsers;

use Firehed\Input\Exceptions\InputException;

/**
 * @coversDefaultClass Firehed\Input\Parsers\URLEncoded
 */
class URLEncodedTest extends \PHPUnit\Framework\TestCase {

    public function validURLEncoded() {
        return [
            ['foo=bar', ['foo' => 'bar']],
            ['a=b&c=d', ['a' => 'b', 'c' => 'd']],
            ['a[]=b&a[]=c', ['a' => ['b', 'c']]],
            ['', []], // Cast empty body to empty array
        ];
    } // validURLEncoded

    public function formatErrors() {
        return [
            ['&'],
        ];
    } // formatErrors

    /**
     * @covers ::parse
     * @dataProvider validURLEncoded
     */
    public function testParse($data, $expected) {
        $parser = new URLEncoded;

        $ret = $parser->parse($data);

        $this->assertEquals($expected, $ret,
            'Parser returned wrong value from URLEncoded');
    }

    /**
     * @covers ::parse
     * @dataProvider formatErrors
     */
    public function testFormatError($data) {
        $parser = new URLEncoded;
        $this->expectException(InputException::class);
        $this->expectExceptionCode(InputException::FORMAT_ERROR);
        $parser->parse($data);
    } // testFormatError
}

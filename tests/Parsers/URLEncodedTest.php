<?php

namespace Firehed\Input\Parsers;

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
     * @expectedException Firehed\Input\Exceptions\InputException
     * @expectedExceptionCode Firehed\Input\Exceptions\InputException::FORMAT_ERROR
     */
    public function testFormatError($data) {
        $parser = new URLEncoded;
        $parser->parse($data);
    } // testFormatError
}

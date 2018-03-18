<?php

namespace Firehed\Input\Parsers;

/**
 * @coversDefaultClass Firehed\Input\Parsers\JSON
 */
class JSONTest extends \PHPUnit\Framework\TestCase {

    public function validJSON() {
        return [
            ['{}', []],
            ['[]', []],
            ['{"foo":"bar"}', ['foo' => 'bar']],
            ['', []], // Cast empty bodies to an empty array
        ];
    } // validJSON

    public function invalidJSON() {
        return [
            ["['123':123]"],
            ['["12"=>"abc"]'],
            ["{'123':123}"],
            ['{"12"=>"abc"}'],
        ];
    } // invalidJSON

    public function formatErrors() {
        return [
            ['true'],
            ['false'],
            ['null'],
            ['1'],
            ['"1"'],
        ];
    } // formatErrors
    /**
     * @covers ::parse
     * @dataProvider validJSON
     */
    public function testParse($json, $expected) {
        $parser = new JSON;

        $ret = $parser->parse($json);

        $this->assertEquals($expected, $ret,
            'Parser returned wrong value from JSON');
    }

    /**
     * @covers ::parse
     * @dataProvider invalidJSON
     * @expectedException Firehed\Input\Exceptions\InputException
     * @expectedExceptionCode Firehed\Input\Exceptions\InputException::PARSE_ERROR
     */
    public function testParseError($json) {
        $parser = new JSON;
        $parser->parse($json);
    } // testParseError

    /**
     * @covers ::parse
     * @dataProvider formatErrors
     * @expectedException Firehed\Input\Exceptions\InputException
     * @expectedExceptionCode Firehed\Input\Exceptions\InputException::FORMAT_ERROR
     */
    public function testFormatError($json) {
        $parser = new JSON;
        $parser->parse($json);
    } // testFormatError
}

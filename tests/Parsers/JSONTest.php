<?php

namespace Firehed\Input\Parsers;

use Firehed\Input\Exceptions\InputException;

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
     */
    public function testParseError($json) {
        $parser = new JSON;
        $this->expectException(InputException::class);
        $this->expectExceptionCode(InputException::PARSE_ERROR);
        $parser->parse($json);
    } // testParseError

    /**
     * @covers ::parse
     * @dataProvider formatErrors
     */
    public function testFormatError($json) {
        $parser = new JSON;
        $this->expectException(InputException::class);
        $this->expectExceptionCode(InputException::FORMAT_ERROR);
        $parser->parse($json);
    } // testFormatError
}

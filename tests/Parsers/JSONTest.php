<?php

namespace Firehed\Input\Parsers;

use Firehed\Input\Exceptions\InputException;

/**
 * @coversDefaultClass Firehed\Input\Parsers\JSON
 */
class JSONTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array{string, mixed[]}[]
     */
    public function validJSON()
    {
        return [
            ['{}', []],
            ['[]', []],
            ['{"foo":"bar"}', ['foo' => 'bar']],
            ['', []], // Cast empty bodies to an empty array
        ];
    }

    /**
     * @return array{string}[]
     */
    public function invalidJSON()
    {
        return [
            ["['123':123]"],
            ['["12"=>"abc"]'],
            ["{'123':123}"],
            ['{"12"=>"abc"}'],
        ];
    }

    /**
     * @return array{string}[]
     */
    public function formatErrors()
    {
        return [
            ['true'],
            ['false'],
            ['null'],
            ['1'],
            ['"1"'],
        ];
    }

    /**
     * @covers ::parse
     * @dataProvider validJSON
     * @param mixed $expected
     */
    public function testParse(string $json, $expected): void
    {
        $parser = new JSON();

        $ret = $parser->parse($json);

        $this->assertEquals(
            $expected,
            $ret,
            'Parser returned wrong value from JSON'
        );
    }

    /**
     * @covers ::parse
     * @dataProvider invalidJSON
     */
    public function testParseError(string $json): void
    {
        $parser = new JSON();
        $this->expectException(InputException::class);
        $this->expectExceptionCode(InputException::PARSE_ERROR);
        $parser->parse($json);
    }

    /**
     * @covers ::parse
     * @dataProvider formatErrors
     */
    public function testFormatError(string $json): void
    {
        $parser = new JSON();
        $this->expectException(InputException::class);
        $this->expectExceptionCode(InputException::FORMAT_ERROR);
        $parser->parse($json);
    }
}

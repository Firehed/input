<?php

declare(strict_types=1);

namespace Firehed\Input\Parsers;

use Firehed\Input\Exceptions\InputException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(JSON::class)]
class JSONTest extends TestCase
{
    /**
     * @return array{string, mixed[]}[]
     */
    public static function validJSON(): array
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
    public static function invalidJSON(): array
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
    public static function formatErrors(): array
    {
        return [
            ['true'],
            ['false'],
            ['null'],
            ['1'],
            ['"1"'],
        ];
    }

    #[DataProvider('validJSON')]
    public function testParse(string $json, mixed $expected): void
    {
        $parser = new JSON();

        $ret = $parser->parse($json);

        $this->assertEquals(
            $expected,
            $ret,
            'Parser returned wrong value from JSON'
        );
    }

    #[DataProvider('invalidJSON')]
    public function testParseError(string $json): void
    {
        $parser = new JSON();
        $this->expectException(InputException::class);
        $this->expectExceptionCode(InputException::PARSE_ERROR);
        $parser->parse($json);
    }

    #[DataProvider('formatErrors')]
    public function testFormatError(string $json): void
    {
        $parser = new JSON();
        $this->expectException(InputException::class);
        $this->expectExceptionCode(InputException::FORMAT_ERROR);
        $parser->parse($json);
    }
}

<?php

namespace Firehed\Input\Sanitizers;

/**
 * @coversDefaultClass Firehed\Input\Sanitizers\XSS
 * @covers ::<protected>
 * @covers ::<private>
 */
class XSSTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::sanitize
     */
    public function testSanitizeWorksRecursively() {
        $input = [
            'key1' => [
                'keysub1' => '<script>',
                'keysub2' => '</script>',
            ],
        ];
        $expected_output = [
            'key1' => [
                'keysub1' => '&lt;script&gt;',
                'keysub2' => '&lt;/script&gt;',
            ],
        ];

        $xss = new XSS;
        $output = $xss->sanitize($input);
        $this->assertEquals($expected_output, $output,
            'Sanitization failed');
    } // testSanitizeWorksRecursively

    /**
     * @dataProvider nonStrings
     * @covers ::sanitize
     */
    public function testSanitizeOnlyTouchesStrings($non_string) {
        $xss = new XSS;
        $data = ['key' => $non_string];
        $output = $xss->sanitize($data);
        $this->assertSame($non_string, $output['key'],
            'A non-string should have been untouched');

    } // testSanitizeOnlyTouchesStrings

    /**
     * @dataProvider strings
     * @covers ::sanitize
     */
    public function testSanitizationOfStringsIsCorrect($in, $exp_out) {
        $xss = new XSS;
        $data = ['key' => $in];
        $out = $xss->sanitize($data);
        $this->assertEquals($exp_out, $out['key'], sprintf(
            "Value was not XSS-escaped correctly (got '%s', expected '%s'",
            $out['key'], $exp_out));
    } // testSanitizationOfStringsIsCorrect

    /**
     * @dataProvider badInput
     * @covers ::sanitize
     * @expectedException InvalidArgumentException
     */
    public function testNonArraysAreRejected($bad) {
        (new XSS())->sanitize($bad);
    } // testNonArraysAreRejected

    public function nonStrings() {
        return [
            [1],
            [null],
            [true],
            [false],
            [238.1],
            [[]],
        ];
    } // nonStrings

    public function strings() {
        return [
            ['&', '&amp;'],
            ['&&', '&amp;&amp;'],
            ['&amp;', '&amp;amp;'], // Something probably double encoded, we
            // are expecting raw input and will not attempt to skip something
            // that's plausibly already encoded
            ['"', '&quot;'],
            ["'", '&#039;'],
            ['<', '&lt;'],
            ['>', '&gt;'],
            ['unchanged', 'unchanged'],
            ['mid<ent', 'mid&lt;ent'],
            ['<img src="x" onerror="alert(1);"/>',
                '&lt;img src=&quot;x&quot; onerror=&quot;alert(1);&quot;/&gt;'],
        ];
    } // strings

    public function badInput() {
        return [
            [["foo" => (object)["bar" => "baz"]]],
            [[1, 2, "three", [4, 5], (object)["six" => "seven"]]],
            [["foo" => new \DateTime()]],
        ];
    } // badJSON
}

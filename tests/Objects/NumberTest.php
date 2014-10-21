<?php

namespace Firehed\Input\Objects;

/**
 * @coversDefaultClass Firehed\Input\Objects\Number
 * @covers ::<protected>
 * @covers ::<private>
 */
class NumberTest extends \PHPUnit_Framework_TestCase {

    private $number;
    public function setUp() {
        $this->number = new Number;
    } // setUp

    // Used by:
    // testInvalidMin
    // testInvalidMax
    public function invalidRangeValues() {
        return [
            [null],
            [false],
            [true],
            ["1.1"],
            ["NAN"],
            ["-2"],
            ["three"],
            [""],
        ];
    } // invalidRangeValues

    // Used by:
    // testValidate
    public function validations() {
        return [
            [null, null, '', false],
            [0, null, '', false],
            [0, null, '1', true],
            [0, null, '-1', false],
            [1, null, '', false],
            [null, 1, '', false],
            [null, 1, 'a', false],
            [null, 1, 'aa', false],
            [null, 0, '0', true],
            [null, 0, '1', false],
            [1, 1, '', false],
            [1, 1, 'a', false],
            [1, 1, 'aa', false],
            [1, 1, '1', true],
            [1, 1, '2', false],
            [1, 1, '0', false],
            [null, null, '1234', true],
            [null, null, 'word', false],
            [null, null, '0555', true],
            [null, null, '1.3e7', true],
            [null, 6, '1.3e9', false],
            [null, null, 1234, true],
            [null, null, true, false],
            [null, null, false, false],
            [null, null, null, false],
        ];
    } // validations

    public function evaluations() {
        return [
            ['123', 123],
            ['123.0', (float)123],
            ['1.2e4', (float)12000],
            ['1.2e-2', 0.012],
            ['0xFF', 255],
        ];
    } // evaluations

    // Used by:
    // testValidMax
    // testValidMin
    public function validRangeValues() {
        return [
            [1],
            [200],
            [255],
            [256],
            [0],
            [-1],
            [-200],
            [-255],
            [-256],
        ];
    } // validRangeValues

    // Used by:
    // testValidMaxMinCombinations
    public function validRangePairs() {
        return [
            [10, 5],
            [10, 10],
            [100, 0],
            [\PHP_INT_MAX, 0],
        ];
    } // validRangePairs

    /**
     * @covers ::setMax
     * @dataProvider invalidRangeValues
     * @expectedException InvalidArgumentException
     */
    public function testInvalidMax($value) {
        $this->number->setMax($value);
    } // testInvalidMax

    /**
     * @covers ::setMax
     * @covers ::validate
     * @dataProvider validRangeValues
     */
    public function testValidMax($value) {
        $this->assertSame($this->number,
            $this->number->setMax($value),
            'setMax should be chainable when called with a valid value');
    } // testValidMax

    /**
     * @covers ::setMin
     * @dataProvider invalidRangeValues
     * @expectedException InvalidArgumentException
     */
    public function testInvalidMin($value) {
        $this->number->setMin($value);
    } // testInvalidMin

    /**
     * @covers ::setMin
     * @covers ::validate
     * @dataProvider validRangeValues
     */
    public function testValidMin($value) {
        $this->assertSame($this->number,
            $this->number->setMin($value),
            'setMin should be chainable when called with a valid value');
    } // testValidMin


    /**
     * @covers ::setMax
     * @covers ::setMin
     * @expectedException InvalidArgumentException
     */
    public function testIncompatibleMaxAfterMin() {
        $this->number->setMin(5)
            ->setMax(4);
    } // testIncompatibleMaxAfterMin

    /**
     * @covers ::setMax
     * @covers ::setMin
     * @expectedException InvalidArgumentException
     */
    public function testIncompatibleMinAfterMax() {
        $this->number->setMax(4)
            ->setMin(5);
    } // testIncompatibleMinAfterMax

    /**
     * @covers ::setMax
     * @covers ::setMin
     * @dataProvider validRangePairs
     */
    public function testValidMaxMinCombinations($max, $min) {
        $this->assertSame($this->number,
            $this->number->setMax($max)->setMin($min),
            'Specified max and min should have been compatible');
    } // testValidMaxMinCombinations

    /**
     * @covers ::setMax
     * @covers ::setMin
     * @covers ::validate
     * @dataProvider validations
     */
    public function testValidate($min, $max, $value, $isValid) {
        if ($min !== null) {
            $this->number->setMin($min);
        }
        if ($max !== null) {
            $this->number->setMax($max);
        }
        $this->number->setValue($value);
        $this->assertSame($isValid,
            $this->number->isValid(),
            'Validation did not match expected output');
    } // testValidate

    /**
     * @covers ::evaluate
     * @dataProvider evaluations
     */
    public function testEvaluate($input_value, $expected_output) {
        $this->assertSame($expected_output,
            $this->number->setValue($input_value)->evaluate(),
            'Evaluated value did not match the expected output');
    } // testEvaluate

}

<?php

namespace Firehed\Input\Objects;

/**
 * @coversDefaultClass Firehed\Input\Objects\Text
 * @covers ::<protected>
 * @covers ::<private>
 */
class TextTest extends \PHPUnit_Framework_TestCase {

    private $string;
    public function setUp() {
        $this->text = new Text;
    } // setUp

    // Used by:
    // testInvalidMin
    // testInvalidMax
    public function invalidRangeValues() {
        return [
            [null],
            [false],
            [true],
            [1.1],
            [-2],
        ];
    } // invalidRangeValues

    // Used by:
    // testValidate
    public function validations() {
        return [
            [null, null, '', true],
            [0, null, '', true],
            [1, null, '', false],
            [null, 1, '', true],
            [null, 1, 'a', true],
            [null, 1, 'aa', false],
            [1, 1, '', false],
            [1, 1, 'a', true],
            [1, 1, 'aa', false],
            [null, null, '1234', true],
            [null, null, 'word', true],
            [null, null, '0555', true],
            [null, null, '1.3e7', true],
            [null, 6, '1.3e9', true], // watch for weird number evaluation
            [null, null, 1234, false],
            [null, null, true, false],
            [null, null, false, false],
            [null, null, null, false],
        ];
    } // validations

    // Used by:
    // testValidMax
    // testValidMin
    public function validRangeValues() {
        return [
            [1],
            [200],
            [255],
            [256],
            [\PHP_INT_MAX]
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
        $this->text->setMax($value);
    } // testInvalidMax

    /**
     * @covers ::setMax
     * @covers ::validate
     * @dataProvider validRangeValues
     */
    public function testValidMax($value) {
        $this->assertSame($this->text,
            $this->text->setMax($value),
            'setMax should be chainable when called with a valid value');
    } // testValidMax

    /**
     * @covers ::setMin
     * @dataProvider invalidRangeValues
     * @expectedException InvalidArgumentException
     */
    public function testInvalidMin($value) {
        $this->text->setMin($value);
    } // testInvalidMin

    /**
     * @covers ::setMin
     * @covers ::validate
     * @dataProvider validRangeValues
     */
    public function testValidMin($value) {
        $this->assertSame($this->text,
            $this->text->setMin($value),
            'setMin should be chainable when called with a valid value');
    } // testValidMin


    /**
     * @covers ::setMax
     * @covers ::setMin
     * @expectedException InvalidArgumentException
     */
    public function testIncompatibleMaxAfterMin() {
        $this->text->setMin(5)
            ->setMax(4);
    } // testIncompatibleMaxAfterMin

    /**
     * @covers ::setMax
     * @covers ::setMin
     * @expectedException InvalidArgumentException
     */
    public function testIncompatibleMinAfterMax() {
        $this->text->setMax(4)
            ->setMin(5);
    } // testIncompatibleMinAfterMax

    /**
     * @covers ::setMax
     * @covers ::setMin
     * @dataProvider validRangePairs
     */
    public function testValidMaxMinCombinations($max, $min) {
        $this->assertSame($this->text,
            $this->text->setMax($max)->setMin($min),
            'Specified max and min should have been compatible');
    } // testValidMaxMinCombinations

    /**
     * @covers ::setMax
     * @expectedException InvalidArgumentException
     */
    public function testMaxOfZeroIsDisallowed() {
        $this->text->setMax(0);
    } // testMaxOfZeroIsDisallowed

    /**
     * @covers ::setMin
     */
    public function testMinOfZeroIsAllowed() {
        $this->assertSame($this->text,
            $this->text->setMin(0),
            'SetMin should allow 0');
    } // testMinOfZeroIsAllowed

    /**
     * @covers ::setMax
     * @covers ::setMin
     * @covers ::validate
     * @dataProvider validations
     */
    public function testValidate($min, $max, $value, $isValid) {
        if ($min !== null) {
            $this->text->setMin($min);
        }
        if ($max !== null) {
            $this->text->setMax($max);
        }
        $this->text->setValue($value);
        $this->assertSame($isValid,
            $this->text->isValid(),
            'Validation did not match expected output');
    } // testValidate

}

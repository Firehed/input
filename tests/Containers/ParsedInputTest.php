<?php

namespace Firehed\Input\Containers;

use Firehed\Input\Objects\InputObject;

/**
 * @coversDefaultClass Firehed\Input\Containers\ParsedInput
 */
class ParsedInputTest extends \PHPUnit_Framework_TestCase {

    // ----(Constructor)--------------------------------------------------------

    /**
     * @covers ::__construct
     */
    public function testConstructWorks() {
        $this->assertInstanceOf('Firehed\Input\Containers\ParsedInput',
            new ParsedInput([]),
            'Construct failed');
    } // testConstructWorks

    // ----(Sanitize)-----------------------------------------------------------
    /**
     * @covers ::sanitize
     * @expectedException InvalidArgumentException
     */
    public function testSanitizeRejectsNonSanitizers() {
        $parsed = new ParsedInput([]);
        $parsed->sanitize(['notAnObject']);
    } // testSanitizeRejectsNonSanitizers

    /**
     * @covers ::sanitize
     */
    public function testSanitize() {
        $unsanitized = ['dirty' => true];
        $sanitized = ['dirty' => false];
        $sanitizer = $this->getMock('Firehed\Input\Interfaces\SanitizerInterface');
        $sanitizer->expects($this->once())
            ->method('sanitize')
            ->with($unsanitized)
            ->will($this->returnValue($sanitized));
        $parsed = new ParsedInput($unsanitized);
        // Sanity check
        $this->assertFalse($parsed->isSanitized(),
            "ValidInput should not be sanitized");
        $this->assertTrue($parsed['dirty'],
            "Bad data was in ValidInput object");

        $ret = $parsed->sanitize([$sanitizer]);
        $this->assertInstanceOf('Firehed\Input\Containers\SanitizedInput',
            $ret,
            'A SanitizedInput object should be returned');
        $this->assertTrue($parsed->isSanitized(),
            "ValidInput should be sanitized");
        $this->assertFalse($ret['dirty'],
            "Return value should be using new sanitized data");
    } // testSanitize



    // ----(ArrayAccess)--------------------------------------------------------

    /**
     * @covers ::offsetGet
     */
    public function testGoodOffset() {
        $array = ['foo' => 'bar'];
        $obj = new ParsedInput($array);
        $this->assertSame('bar',
            $obj['foo'],
            'The value of the parsed input was not the same as the input');
    } // testGoodOffset

    /**
     * @covers ::offsetGet
     * @expectedException DomainException
     */
    public function testBadOffset() {
        $obj = new ParsedInput([]);
        $data = $obj['foo'];
    } // testBadOffset

    /**
     * @covers ::offsetGet
     */
    public function testAccessOfExpectedNullValue() {
        // isset() returns false on null, so assert there's no weirdness
        $obj = new ParsedInput(['foo' => null]);
        $this->assertNull($obj['foo'], 'The wrong value was returned');
    } // testAccessOfExpectedNullValue


    /**
     * @covers ::offsetExists
     * @expectedException BadMethodCallException
     */
    public function testIssetThrows() {
        $obj = new ParsedInput([]);
        isset($obj['foo']);
    } // testIssetThrows

    /**
     * @covers ::offsetExists
     * @expectedException BadMethodCallException
     */
    public function testEmptyThrows() {
        $obj = new ParsedInput([]);
        empty($obj['foo']);
    } // testEmptyThrows

    /**
     * @covers ::offsetUnset
     * @expectedException BadMethodCallException
     */
    public function testUnsetThrows() {
        $obj = new ParsedInput([]);
        unset($obj['foo']);
    } // testUnsetThrows

    /**
     * @covers ::offsetSet
     * @expectedException BadMethodCallException
     */
    public function testSetThrows() {
        $obj = new ParsedInput([]);
        $obj['foo'] = 'bar';
    } // testSetThrows


}

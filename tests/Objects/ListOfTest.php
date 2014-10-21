<?php

namespace Firehed\Input\Objects;

use PHPUnit_Framework_MockObject_Stub_ConsecutiveCalls as OCC;

/**
 * @coversDefaultClass Firehed\Input\Objects\ListOf
 */
class ListOfTest extends \PHPUnit_Framework_TestCase {

    /** @covers ::__construct */
    public function testConstruct() {
        $io = $this->getMockForAbstractClass('Firehed\Input\Objects\InputObject');
        $this->assertInstanceOf('Firehed\Input\Objects\ListOf',
            new ListOf($io),
            'Construct failed');
    } // testConstruct

    /**
     * @covers ::validate
     * @dataProvider values
     */
    public function testValidate($input, $mock_returns, $is_valid) {
        // Generally, assert that each of the input values is validated against
        // the input type provided in the constructor
        $io = $this->getMockForAbstractClass('Firehed\Input\Objects\InputObject',
            ['validate']);
        $io->expects($this->atLeastOnce())
            ->method('validate')
            // ->will($this->onConsectiveCalls(...$mock_returns));
            ->will(new OCC($mock_returns));

        $list_of = new ListOf($io);
        $list_of->setValue($input);

        if ($is_valid) {
            $this->assertTrue($list_of->isValid(), 'Value should be valid');
        }
        else {
            $this->assertFalse($list_of->isValid(), 'Value should be invalid');
        }
    } // testValidate

    /**
     * @covers ::evaluate
     * @dataProvider values
     */
    public function testEvaluate($input, $mock_returns, $is_valid) {
        $io = $this->getMock('Firehed\Input\Objects\InputObject',
            ['validate', 'evaluate']);
        $map = [];
        $out_map = [];
        foreach ($input as $i => $value) {
            $map[$i] = [$value, $mock_returns[$i]];
            $out_map[$i] = new \StdClass;
        }

        $io->expects($this->any())
            ->method('validate')
            ->will($this->returnValueMap($map));

        $list_of = new ListOf($io);
        $list_of->setValue($input);
        if ($is_valid) {
            $io->expects($this->atLeastOnce())
                ->method('evaluate')
                ->will(new OCC($out_map));
            $this->assertSame($out_map, $list_of->evaluate(),
                'The evaluated list did not return the child evaluate() values');
        }
        else {

            $this->setExpectedException('UnexpectedValueException');
            $list_of->evaluate();
        }
    } // testEvaluate

    /**
     * @dataProvider nonLists
     * @covers ::validate
     */
    public function testNonListsAreRejected($non_list) {
        $io = $this->getMockForAbstractClass('Firehed\Input\Objects\InputObject');
        $io->expects($this->never())
            ->method('validate');
        $list_of = new ListOf($io);
        $list_of->setValue($non_list);
        $this->assertFalse($list_of->isValid());
    } // testNonListsAreRejected

    public function values() {
        return [
            [['value'], [true], true],
            [['value'], [false], false],

            [['v1', 'v2'], [true, true], true],
            [['v1', 'v2'], [false, false], false],
            [['v1', 'v2'], [true, false], false],
            [['v1', 'v2'], [false, true], false],
        ];
    } // values

    public function nonLists() {
        return [
            [1],
            ['string'],
            [false],
            [null],
            [['key' => 'value']], // dict, not list
        ];
    } // nonLists

}

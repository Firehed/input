<?php

namespace Firehed\Input\Containers;

use Firehed\Input\Interfaces\ParserInterface;

class RawInput {

    private $data;

    private $is_parsed = false;
    private $is_validated = false;

    public function __construct($raw) {
        $this->data = $raw;
    } // __construct

    public function parse(ParserInterface $parser) {
        $this->setData($parser->parse($this->getData()))
            ->setIsParsed(true);
        return new ParsedInput($this->getData());
    } // parse

    // Not final so it can be mocked; do not extend
    protected function getData() {
        return $this->data;
    } // getData

    final protected function setData(array $data) {
        $this->data = $data;
        return $this;
    } // setData

    // Do not extend these; they are not declared as final so that they can be
    // mocked during unit tests
    public function isParsed() {
        return $this->is_parsed;
    } // isParsed
    public function isValidated() {
        return $this->is_validated;
    } // isValidated

    final protected function setIsParsed($bool) {
        $this->is_parsed = (bool)$bool;
        return $this;
    } // setIsParsed
    final protected function setIsValidated($bool) {
        $this->is_validated = (bool)$bool;
        return $this;
    } // setIsValidated

}

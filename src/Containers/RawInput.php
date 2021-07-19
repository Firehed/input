<?php

declare(strict_types=1);

namespace Firehed\Input\Containers;

use Firehed\Input\Interfaces\ParserInterface;

class RawInput
{

    private $data;

    private $is_parsed = false;
    private $is_validated = false;

    public function __construct($raw)
    {
        $this->data = $raw;
    }

    public function parse(ParserInterface $parser): ParsedInput
    {
        $this->setData($parser->parse($this->getData()))
            ->setIsParsed(true);
        return new ParsedInput($this->getData());
    }

    // Not final so it can be mocked; do not extend
    protected function getData()
    {
        return $this->data;
    }

    final protected function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    // Do not extend these; they are not declared as final so that they can be
    // mocked during unit tests
    public function isParsed(): bool
    {
        return $this->is_parsed;
    }
    public function isValidated()
    {
        return $this->is_validated;
    }

    final protected function setIsParsed(bool $bool): self
    {
        $this->is_parsed = $bool;
        return $this;
    }
    final protected function setIsValidated(bool $bool): self
    {
        $this->is_validated = $bool;
        return $this;
    }
}

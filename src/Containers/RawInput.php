<?php

declare(strict_types=1);

namespace Firehed\Input\Containers;

use Firehed\Input\Interfaces\ParserInterface;

class RawInput
{
    private mixed $data;
    private bool $isParsed = false;
    private bool $isValidated = false;

    /**
     * @param mixed $raw
     */
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

    /**
     * Not final so it can be mocked; do not extend
     * @return mixed
     */
    protected function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed[] $data
     */
    final protected function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    // Do not extend these; they are not declared as final so that they can be
    // mocked during unit tests
    public function isParsed(): bool
    {
        return $this->isParsed;
    }

    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    final protected function setIsParsed(bool $bool): self
    {
        $this->isParsed = $bool;
        return $this;
    }

    final protected function setIsValidated(bool $bool): self
    {
        $this->isValidated = $bool;
        return $this;
    }
}

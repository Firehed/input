<?php

namespace Firehed\Input\Interfaces;

interface ParserInterface {

    /**
     * @param string Unparsed, unvalidated input
     * @return array Parsed, unvalidated input
     * @throws \ApiException
     */
    public function parse($raw_input);

    /**
     * @return array<string> Supported mime types
     */
    public function getSupportedMimeTypes();

}

<?php

namespace Firehed\Input\Interfaces;

interface ValidationInterface {

    /**
     * @return array<string, Objects\InputObject>
     */
    public function getRequiredInputs();

    /**
     * @return array<string, Objects\InputObject>
     */
    public function getOptionalInputs();

}

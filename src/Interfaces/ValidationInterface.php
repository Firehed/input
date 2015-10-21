<?php

declare(strict_types=1);

namespace Firehed\Input\Interfaces;

interface ValidationInterface {

    /**
     * @return array<string, Objects\InputObject>
     */
    public function getRequiredInputs(): array;

    /**
     * @return array<string, Objects\InputObject>
     */
    public function getOptionalInputs(): array;

}

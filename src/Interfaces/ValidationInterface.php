<?php

declare(strict_types=1);

namespace Firehed\Input\Interfaces;

use Firehed\Input\Objects\InputObject;

interface ValidationInterface
{

    /**
     * @return array<string, InputObject>
     */
    public function getRequiredInputs(): array;

    /**
     * @return array<string, InputObject>
     */
    public function getOptionalInputs(): array;
}

<?php

declare(strict_types=1);

namespace Firehed\Input\Validators;

use stdClass;

use function is_array;

class Dictionary implements ValidatorInterface
{
    /**
     * @param ValidatorInterface[] $required
     * @param ValidatorInterface[] $optional
     */
    public function __construct(
        private array $required,
        private array $optional = [],
        private bool $rejectUnexpected = true,
    ) {
    }

    public function validate(mixed $decoded): Result
    {
        return match (true) {
            is_array($decoded) => $this->validateArray($decoded),
            $decoded instanceof stdClass => $this->validateObject($decoded),
        };
    }

    private function validateArray(array $decoded)
    {
        foreach ($decoded as $key => $value) {
            // if in required, force validation
            // if in optional, still force validation
            // if in neither, examine $rejectUnexpected
        }
        // check if any optional values are not yet in the output & apply their
        // default values
    }

    private function validateObject(stdClass $decoded)
    {
    }

    public function getDefaultValue()
    {
    }
}

<?php

namespace Firehed\Input\Containers;

use BadMethodCallException;
use UnexpectedValueException;
use Firehed\Input\Exceptions\InputException;
use Firehed\Input\Interfaces\ValidationInterface;
use Firehed\Input\Interfaces\URIValidationInterface;

class SanitizedInput extends ParsedInput {

    public function __construct(ParsedInput $input) {
        if (!$input->isSanitized()) {
            throw new BadMethodCallException;
        }
        $this->setData($input->getData());
    } // __construct

    /**
     * @param Firehed\Input\Interfaces\ValidationInterface Validation requirements
     * @return Firehed\Input\Containers\ValidInput
     * @throws Firehed\Input\Exceptions\InputException
     */
    public function validate(ValidationInterface $validator) {

        $data = $this->getData();
        $clean_out = [];
        $missing = [];
        $invalid = [];


        foreach ($validator->getRequiredInputs() as $key => $input) {
            if (!isset($data[$key]) && !array_key_exists($key, $data)) {
                $missing[] = $key;
                continue;
            }

            try {
                $clean_out[$key] = $input->setValue($data[$key])
                    ->evaluate();
                unset($data[$key]);
            }
            catch (UnexpectedValueException $e) {
                $invalid[] = $key;
            }
        } unset($key, $input);

        foreach ($validator->getOptionalInputs() as $key => $input) {
            if (isset($data[$key])) {
                try {
                    $clean_out[$key] = $input->setValue($data[$key])
                        ->evaluate();
                    unset($data[$key]);
                }
                catch (UnexpectedValueException $e) {
                    $invalid[] = $key;
                }
            }
            else {
                // Somehow, there should be a concept of "use default
                // value" (null unless overridden) so that optional inputs
                // can correctly be resolved as dependencies
                $clean_out[$key] = null;
                unset($data[$key]); // in case of literal null value
            }
        } unset($key, $input);

        if ($missing) {
            throw new InputException(InputException::MISSING_VALUES, $missing);
        }
        if ($invalid) {
            throw new InputException(InputException::INVALID_VALUES, $invalid);
        }
        if ($data) {
            throw new InputException(InputException::UNEXPECTED_VALUES, $data);
        }

        $this->setData($clean_out);
        $this->setIsValidated(true);

        return new SafeInput($this);
    }

}

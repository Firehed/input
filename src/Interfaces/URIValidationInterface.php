<?php

namespace Firehed\Input\Interfaces;

interface URIValidationInterface extends ValidationInterface {

    /**
     * These are additional required input values and their types, similar to
     * getRequiredInputs() in the parent interface. The reason they are not
     * combined is so that it will be easy to logically separate the two in
     * generated documentation, SDKs, etc.
     *
     * If a class implements this interface, it will automatically get merged
     * into the SafeInput data like both optional and required parameters.
     *
     * @return array<string, Objects\InputObject>
     */
    public function getURIInputs();

}

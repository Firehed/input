<?php

namespace Firehed\Input\Interfaces;

interface SanitizerProviderInterface {

    /**
     * @return array<SanitizerInterface>
     */
    public function getSanitizationFilters();

}

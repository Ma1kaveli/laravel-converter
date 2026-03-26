<?php

namespace Converter\Helpers;

use Converter\CaseConverter;
use Converter\Constants\CaseConstants;

class ConverterHelpers {
    /**
     * Proccess result to needly format.
     *
     * @param mixed $resource
     *
     * @return array
     */
    public static function convertResult($resource) {
        return resolve(CaseConverter::class)->convert(
            config('converter.convert_from') ?? CaseConstants::CASE_CAMEL,
            json_decode($resource->toJson(), true)
        );
    }
}

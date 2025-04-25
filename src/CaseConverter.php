<?php

namespace LaravelConverter;

use LaravelConverter\Constants\CaseConstants;

use Illuminate\Support\Str;
use InvalidArgumentException;

class CaseConverter
{
    private $arguments = [
        CaseConstants::CASE_SNAKE,
        CaseConstants::CASE_CAMEL,
        CaseConstants::CASE_KEBAB,
        CaseConstants::CASE_STUDLY,
    ];

    /**
     * Convert an array to a given case.
     *
     * @param string $case
     * @param array $data
     * @return array
     */
    public function convert(string $case, array $data): array
    {
        if (!in_array($case, $this->arguments)) {
            throw new InvalidArgumentException(
                'Case unknown, possible casing: ' . implode(',', $this->arguments)
            );
        }

        $array = [];

        foreach ($data as $key => $value) {
            $array[Str::{$case}($key)] = is_array($value)
                ? $this->convert($case, $value)
                : $value;
        }

        return $array;
    }
}

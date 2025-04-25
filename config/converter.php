<?php

use LaravelConverter\Constants\CaseConstants;

return [
    'convert_from' => CaseConstants::CASE_CAMEL,

    'convert_to' => CaseConstants::CASE_SNAKE,

    'query_params' => [

        'descending_default_value' => 'asc',

        'sort_by_default_value' => 'created_at',

        'request_sort_by' => [
            'name' => 'sort_by',

            'descending_key' => 'descending',

            'sort_by_key' => 'value',
        ],

        'return_descending_name' => 'descending',

        'return_sort_by_name' => 'sort_by',
    ],
];

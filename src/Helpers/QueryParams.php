<?php

namespace Converter\Helpers;

class QueryParams
{

    /**
     * defaultPaginationParamsKeys
     *
     * @return array
     */
    public static function defaultPaginationParamsKeys(): array
    {
        return [
            'showDeleted',
            'rowsPerPage',
            'rowsNumber',
            'page',
            'sortBy'
        ];
    }

    /**
     * getSortParams
     *
     * @param array $params
     * @param array $productSortTypes
     *
     * @return array
     */
    public static function getSortParams(array $params, array $sortTypes): array
    {
        $params[
            config('converter.query_params.return_descending_name')
        ] = !array_key_exists(
            $params[config('converter.query_params.request_sort_by.name')],
            $sortTypes
        )
            ? config('converter.query_params.descending_default_value')
            : $sortTypes[
                config('converter.query_params.request_sort_by.name')
            ][config('converter.query_params.request_sort_by.descending_key')];

        $params[
            config('converter.query_params.return_sort_by_name')
        ] = !array_key_exists(
            $params[config('converter.query_params.request_sort_by.name')],
            $sortTypes
        )
            ? config('converter.query_params.sort_by_default_value')
            : $sortTypes[
                $params[config('converter.query_params.request_sort_by.name')]
            ][config('converter.query_params.request_sort_by.sort_by_key')];

        return $params;
    }

    /**
     * convertArrStrToArrNumber
     *
     * @param array $params
     *
     * @return array
     */
    public static function convertArrStrToArrNumber(array $params): array
    {
        if (empty($params)) {
            return $params;
        }

        if (!is_array($params) || count($params) < 1) {
            return [(int) $params];
        }

        foreach ($params as $key => $value) {
            $params[$key] = (int) $value;
        }

        return $params;
    }

    /**
     * mapSortBy
     *
     * @param array $params
     * @param array $mapper
     * @param string $sortByKey
     *
     * @return array
     */
    public static function mapSortBy(
        array $params,
        array $mapper,
        string $sortByKey = 'sort_by'
    ): array {
        foreach ($params as $key => $value) {
            if (($key !== $sortByKey) || !is_string($value)) continue;

            if (!isset($mapper[$value])) continue;

            $params[$key] = $mapper[$value];
        }

        return $params;
    }
}

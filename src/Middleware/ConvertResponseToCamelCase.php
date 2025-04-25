<?php

namespace LaravelConverter\Middleware;

use LaravelConverter\CaseConverter;
use LaravelConverter\Constants\CaseConstants;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConvertResponseToCamelCase
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $converted = resolve(CaseConverter::class)->convert(
                config('converter.convert_from') ?? CaseConstants::CASE_CAMEL,
                json_decode($response->content(), true)
            );

            $response->setData($converted);
        }

        return $response;
    }
}

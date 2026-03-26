# makaveli/laravel-converter

[![Packagist Version](https://img.shields.io/packagist/v/makaveli/laravel-converter.svg?style=flat-square)](https://packagist.org/packages/makaveli/laravel-converter)
[![Packagist Downloads](https://img.shields.io/packagist/dt/makaveli/laravel-converter.svg?style=flat-square)](https://packagist.org/packages/makaveli/laravel-converter)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

## 🌍 Languages

- 🇺🇸 English (default)
- 🇷🇺 [Русская версия](docs/ru/README.md)

## Table of Contents

1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Main components](#main-components)
5. [Examples](#examples)
6. [Recommendations](#recommendations)

## Introduction

The **makaveli/laravel-converter** library provides utilities for data transformation in Laravel applications.
This library allows you to transform array keys between different naming conventions (snake_case, camelCase, kebab-case, StudlyCase), proccess query params in requests and automatically convert data for response. It simplifies working with data, especially in APIs where consistent key naming is important.

Main goals for this library are:
- Automatically convert array keys and requests.
- Prepare sort and pagination in query params
- Integration with Laravel ^10+ and PHP 8.2+
- License: MIT.
- Author: Michael Udovenko.

## ## Installation

1. Download library from Composer:
    ```
    composer require makaveli/laravel-converter
    ```

2. Publish configuration file (this will create `config/converter.php` file in your project):
    ```
    php artisan vendor:publish --tag=converter-config
    ```

3. Register provider in `config/app.php` file (if it's not registered automatically)
    ```php
   'providers' => [
       // ...
       \Converter\Providers\ConverterServiceProvider::class,
   ],
   ```

4. *(optional)* Add middleware to convert your responses in `app/Http/Kernel.php`:
   ```php
   protected $middleware = [
       // ...
       \Converter\Middleware\ConvertResponseTo::class,
   ];
   ```

## Configuration

Configuration file `config/converter.php` includes the following settings:
- `'convert_from' => CaseConstants::CASE_CAMEL`: Original keys style (for convert from).
- `'convert_to' => CaseConstants::CASE_SNAKE`: Target keys style (for convert to).
- `'query_params'`: Settings for proccess query params.
  - `'descending_default_value' => 'asc'`: Default direction value for sort (ascending/descending).
  - `'sort_by_default_value' => 'created_at'`: Default field value for sort.
  - `'request_sort_by'`: Keys for sort in request (`'name'`, `'descending_key'`, `'sort_by_key'`).
  - `'return_descending_name' => 'descending'`: Return param name for direction sort value.
  - `'return_sort_by_name' => 'sort_by'`: Return param name for sort value.

You can override these values for adapt them to your project.

## Main components

The library is divided into modules (namespace `Converter`). Below are the main components.

### Constants

- **CaseConstants**: Include case styles:
  - `CASE_SNAKE` ('snake' — for snake_case).
  - `CASE_CAMEL` ('camel' — for camelCase).
  - `CASE_KEBAB` ('kebab' — for kebab-case).
  - `CASE_STUDLY` ('studly' — for StudlyCase).

### DTO

- **ConverterDTO**: Base class for convert data.
  - `__constructor` is init `CaseConverter` class.
  - `getQueryParams(Request $request, array $paramNames, ?array $sortByMapper = null, string $sortByKey = 'sort_by')` extracts and converts query params (with sort mapping).
  - `getRequestData(array $data)`: converts array data to target case (from config).

### Helper methods

- **ConverterHelpers**: Advantage methods which use `CaseConverter` class
  - `convertResult(mixed $resource)` Converts the result to the desired format

- **QueryParams**: Advantage methods for query params
  - `defaultPaginationParamsKeys()`: Returns default keys for pagination (showDeleted, rowsPerPage and etc.).
  - `getSortParams(array $params, array $sortTypes)`: Proccesses sort params with default values.
  - `convertArrStrToArrNumber(array $params)`: Conversts from an array of strings to array of numbers.
  - `mapSortBy(array $params, array $mapper, string $sortByKey = 'sort_by')`: Mappings fields of sort.

### Middleware

- **ConvertResponseTo**: Middleware for automatically converts JSON responses to other case from config (from-case).
Can be used globally or for specific routes.

### Providers

- **ConverterServiceProvider**: Registers and publishes config.

### Core class

- **CaseConverter**: Main converter.

  - `convert(string $case, array $data)`: Recursive converts array keys to certain case. Supports nested arrays. Throw `InvalidArgumentException` class if case is unknown.

## Examples

### Convert array
```php
use Converter\CaseConverter;
use Converter\Constants\CaseConstants;

$converter = new CaseConverter();
$data = ['first_name' => 'John', 'last_name' => 'Doe'];
$converted = $converter->convert(CaseConstants::CASE_CAMEL, $data);
// Result: ['firstName' => 'John', 'lastName' => 'Doe']
```

### Convert Eloquent Model or Collection (or other object that has toJson() method)
```php
$user = User::findOrFail($id);
$users = User::all();

return response()->json([
    'user' => ConverterHelpers::convertResult($user),
    'users' => ConverterHelpers::convertResult($users)
]);
// If you have CaseConstants::CASE_CAMEL in your config file all fields of user model and collection will be converted from snake_case to camelCase
```

### Proccess query-params
```php
use Converter\DTO\ConverterDTO;
use Illuminate\Http\Request;

$dto = new ConverterDTO();
$params = ConverterDTO::getQueryParams($request, ['sort_by', 'descending'], ['price' => 'price_asc']);
// Converts and maps to snake_case (by config).
```

### Middleware

After adding `ConvertResponseTo` middleware to `Kernel.php` all yours JSON-reponses will be automatically convert (for example from case_snake to camelCase).

## Recommendations

- Use in APIs to maintain consistent key naming.
- Configure it for integration with other libraries (for example `makaveli/laravel-core`).
- For nested data converter works recursive.
- If you need custom case add it to `CaseConstants` and refresh validation.

This library is easy and lightweight so it's perfect for utilitarian tasks. 
If you need extra information visit [GitHub page for this project](https://github.com/Ma1kaveli/laravel-converter)

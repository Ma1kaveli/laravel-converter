# makaveli/laravel-converter

[![Packagist Version](https://img.shields.io/packagist/v/makaveli/laravel-converter.svg?style=flat-square)](https://packagist.org/packages/makaveli/laravel-converter)
[![Packagist Downloads](https://img.shields.io/packagist/dt/makaveli/laravel-converter.svg?style=flat-square)](https://packagist.org/packages/makaveli/laravel-converter)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

## Содержание

1. [Введение](#введение)
2. [Установка](#установка)
3. [Конфигурация](#конфигурация)
4. [Основные компоненты](#основные-компоненты)
    - [Константы](#константы)
    - [DTO](#dto)
    - [Helper методы](#helper-методы)
    - [Middleware](#middleware)
    - [Providers](#providers)
    - [Core класс](#core-класс)
5. [Примеры](#примеры)
    - [Конвертация массива](#конвертация-массива)
    - [Конвертация Eloquent модели или коллекции (или другого объекта, который содержит toJson() метод)](#конвертация-eloquent-модели-или-коллекции-или-другого-объекта-который-содержит-tojson-метод)
    - [Обработка query-параметров](#обработка-query-параметров)
    - [Middleware](#middleware-1)
6. [Рекомендации](#рекомендации)

## Введение

Библиотека **makaveli/laravel-converter** предоставляет утилиты для конвертации данных в Laravel-приложениях. Она фокусируется на преобразовании ключей массивов между различными стилями написания (snake_case, camelCase, kebab-case, StudlyCase), обработке query-параметров запросов и автоматической конвертации ответов. Это упрощает работу с данными, особенно в API, где consistency в стиле ключей важна.

Основные цели библиотеки:
- Автоматическая конвертация ключей в массивах и запросах.
- Обработка сортировки и пагинации в query-параметрах.
- Интеграция с Laravel ^10+ и PHP 8.2+.
- Лицензия: MIT.
- Автор: Michael Udovenko.

## Установка

1. Установите библиотеку через Composer:
    ```
    composer require makaveli/laravel-converter
    ```

2. Опубликуйте конфигурационный файл (это создаст `config/converter.php` файл в вашем проекте):
    ```
    php artisan vendor:publish --tag=converter-config
    ```

3. Зарегистрируйте провайдер в `config/app.php` файле (если не зарегистрирован автоматически)
    ```php
   'providers' => [
       // ...
       \Converter\Providers\ConverterServiceProvider::class,
   ],
   ```

4. *(Опционально)* Добавьте middleware для конвертации ответов в `app/Http/Kernel.php`:
   ```php
   protected $middleware = [
       // ...
       \Converter\Middleware\ConvertResponseTo::class,
   ];
   ```

## Конфигурация

Конфигурационный файл `config/converter.php` содержит настройки:
- `'convert_from' => CaseConstants::CASE_CAMEL`: Исходный стиль ключей (для конвертации из).
- `'convert_to' => CaseConstants::CASE_SNAKE`: Целевой стиль ключей (для конвертации в).
- `'query_params'`: Настройки для обработки query-параметров.
  - `'descending_default_value' => 'asc'`: Значение по умолчанию для сортировки (ascending/descending).
  - `'sort_by_default_value' => 'created_at'`: Поле сортировки по умолчанию.
  - `'request_sort_by'`: Ключи для сортировки в запросе (`'name'`, `'descending_key'`, `'sort_by_key'`).
  - `'return_descending_name' => 'descending'`: Имя возвращаемого параметра для направления сортировки.
  - `'return_sort_by_name' => 'sort_by'`: Имя возвращаемого параметра для поля сортировки.

Вы можете переопределить эти значения для адаптации под ваш проект.

## Основные компоненты

Библиотека разделена на модули (неймспейс `Converter`). Ниже описаны ключевые части.

### Константы

- **CaseConstants**: Определяет стили case:
  - `CASE_SNAKE` ('snake' — для snake_case).
  - `CASE_CAMEL` ('camel' — для camelCase).
  - `CASE_KEBAB` ('kebab' — для kebab-case).
  - `CASE_STUDLY` ('studly' — для StudlyCase).

### DTO

- **ConverterDTO**: Основной класс для конвертации данных.
  - `__constructor` инициализирует `CaseConverter` класс.
  - `getQueryParams(Request $request, array $paramNames, ?array $sortByMapper = null, string $sortByKey = 'sort_by')` извлекает и конвертирует query-параметры (с поддержкой маппинга сортировки).
  - `getRequestData(array $data)`: конвертирует массив данных в целевой case (из конфига).

### Helper методы

- **ConverterHelpers**: Дополнительные методы для `CaseConverter` класса
  - `convertResult(mixed $resource)` Конвертирует результат в нужный формат

- **QueryParams**: Вспомогательные методы для query-параметров
  - `defaultPaginationParamsKeys()`: Возвращает ключи по умолчанию для пагинации (showDeleted, rowsPerPage и т.д.).
  - `getSortParams(array $params, array $sortTypes)`: Обрабатывает параметры сортировки с дефолтами.
  - `convertArrStrToArrNumber(array $params)`: Конвертирует массив строк в числа.
  - `mapSortBy(array $params, array $mapper, string $sortByKey = 'sort_by')`: Маппит значения сортировки.

### Middleware

- **ConvertResponseTo**: Middleware для автоматической конвертации JSON-ответов в camelCase (или другой from-case из конфига). Применяется глобально или к роутам.

### Providers

- **ConverterServiceProvider**: Регистрирует и публикует конфиг.

### Core класс

- **CaseConverter**: Основной конвертер.

  - `convert(string $case, array $data)`: Рекурсивно конвертирует ключи массива в указанный case. Поддерживает вложенные массивы. Бросает `InvalidArgumentException` при неизвестном case.

## Примеры

### Конвертация массива
```php
use Converter\CaseConverter;
use Converter\Constants\CaseConstants;

$converter = new CaseConverter();
$data = ['first_name' => 'John', 'last_name' => 'Doe'];
$converted = $converter->convert(CaseConstants::CASE_CAMEL, $data);
// Результат: ['firstName' => 'John', 'lastName' => 'Doe']
```

### Конвертация Eloquent модели или коллекции (или другого объекта, который содержит toJson() метод)
```php
$user = User::findOrFail($id);
$users = User::all();

return response()->json([
    'user' => ConverterHelpers::convertResult($user),
    'users' => ConverterHelpers::convertResult($users)
]);
// Если у вас CaseConstants::CASE_CAMEL в вашем конфигурационном файле, то все поля user модели и коллекции будут конвертированы из snake_case в camelCase
```

### Обработка query-параметров
```php
use Converter\DTO\ConverterDTO;
use Illuminate\Http\Request;

$dto = new ConverterDTO();
$params = ConverterDTO::getQueryParams($request, ['sort_by', 'descending'], ['price' => 'price_asc']);
// Конвертирует и маппит параметры в snake_case (по конфигу).
```

### Middleware

После добавления Middleware `ConvertResponseTo` в `Kernel.php` все ваши JSON-ответы будут автоматически конвертироваться (для примера из case_snake в camelCase).

## Рекомендации

- Используйте в API для consistency стилей ключей.
- Настройте конфиг для интеграции с другими библиотеками (например `makaveli/laravel-core`).
- Для вложенных данных конвертер работает рекурсивно.
- Если нужен кастомный case, добавьте в `CaseConstants` и обновите валидацию.

Библиотека проста и легковесна, идеальна для утилитарных задач.
Если нужны дополнения, обратитесь к исходному коду на [GitHub странице данного проекта](https://github.com/Ma1kaveli/laravel-converter)

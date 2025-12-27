# Документация к библиотеке "makaveli/laravel-converter"

## Введение

Библиотека **makaveli/laravel-converter** предоставляет утилиты для продвинутой конвертации данных в Laravel-приложениях. Она фокусируется на преобразовании ключей массивов между различными стилями написания (snake_case, camelCase, kebab-case, studlyCase), обработке query-параметров запросов и автоматической конвертации ответов. Это упрощает работу с данными, особенно в API, где consistency в стиле ключей важна.

Основные цели библиотеки:
- Автоматическая конвертация ключей в массивах и запросах.
- Обработка сортировки и пагинации в query-параметрах.
- Интеграция с Laravel (версии 10-12) и PHP 8.2+.
- Лицензия: MIT.
- Автор: Michael Udovenko.

Библиотека предназначена как зависимость для других пакетов (например, `makaveli/laravel-core`), но может использоваться самостоятельно.

## Установка

1. Установите библиотеку через Composer:
   ```
   composer require makaveli/laravel-converter
   ```

2. Опубликуйте конфигурационный файл:
   ```
   php artisan vendor:publish --tag=converter-config
   ```
   Это создаст файл `config/converter.php` в вашем проекте.

3. Зарегистрируйте провайдер в `config/app.php` (если не зарегистрирован автоматически):
   ```php
   'providers' => [
       // ...
       \Converter\Providers\ConverterServiceProvider::class,
   ],
   ```

4. (Опционально) Добавьте middleware для конвертации ответов в `app/Http/Kernel.php`:
   ```php
   protected $middleware = [
       // ...
       \Converter\Middleware\ConvertResponseToCamelCase::class,
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
  - Конструктор: Инициализирует `CaseConverter`.
  - `getQueryParams(Request $request, array $paramNames, ?array $sortByMapper = null, string $sortByKey = 'sort_by')`: Извлекает и конвертирует query-параметры (с поддержкой маппинга сортировки).
  - `getRequestData(array $data)`: Конвертирует массив данных в целевой case (из конфига).

### Helpers

- **QueryParams**: Вспомогательные методы для query-параметров.
  - `defaultPaginationParamsKeys()`: Возвращает ключи по умолчанию для пагинации (showDeleted, rowsPerPage и т.д.).
  - `getSortParams(array $params, array $sortTypes)`: Обрабатывает параметры сортировки с дефолтами.
  - `convertArrStrToArrNumber(array $params)`: Конвертирует массив строк в числа.
  - `mapSortBy(array $params, array $mapper, string $sortByKey = 'sort_by')`: Маппит значения сортировки.

### Middleware

- **ConvertResponseToCamelCase**: Middleware для автоматической конвертации JSON-ответов в camelCase (или другой from-case из конфига). Применяется глобально или к роутам.

### Providers

- **ConverterServiceProvider**: Регистрирует и публикует конфиг.

### Core Класс

- **CaseConverter**: Основной конвертер.
  - `convert(string $case, array $data)`: Рекурсивно конвертирует ключи массива в указанный case. Поддерживает вложенные массивы. Бросает исключение при неизвестном case.

## Примеры использования

### Конвертация массива
```php
use Converter\CaseConverter;
use Converter\Constants\CaseConstants;

$converter = new CaseConverter();
$data = ['first_name' => 'John', 'last_name' => 'Doe'];
$converted = $converter->convert(CaseConstants::CASE_CAMEL, $data);
// Результат: ['firstName' => 'John', 'lastName' => 'Doe']
```

### Обработка query-параметров
```php
use Converter\DTO\ConverterDTO;
use Illuminate\Http\Request;

$dto = new ConverterDTO();
$params = ConverterDTO::getQueryParams($request, ['sort_by', 'descending'], ['price' => 'price_asc']);
// Конвертирует и маппит параметры в snake_case (по конфигу).
```

### Middleware в действии
После добавления middleware, все JSON-ответы автоматически конвертируются (например, из snake_case в camelCase).

## Рекомендации

- Используйте в API для consistency стилей ключей.
- Настройте конфиг для интеграции с другими библиотеками (например, `laravel-core`).
- Для вложенных данных конвертер работает рекурсивно.
- Если нужен кастомный case, добавьте в `CaseConstants` и обновите валидацию.

Библиотека проста и легковесна, идеальна для утилитарных задач. Если нужны дополнения, обратитесь к исходному коду на GitHub: https://github.com/Ma1kaveli/laravel-converter.

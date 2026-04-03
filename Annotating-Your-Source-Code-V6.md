# Annotating Your Source Code


This guide covers how to annotate your PHP source code for Phan V6, including all union type syntax, PHPDoc annotations, and new V6 features like generic types, variance annotations, and utility types.

## Table of Contents

1. [Basic Annotations](#basic-annotations)
2. [Union Type Syntax](#union-type-syntax)
3. [Multiline Doc Comments (V6)](#multiline-doc-comments-v6)
4. [Generic Type Annotations (V6)](#generic-type-annotations-v6)
5. [Template Constraints (V6)](#template-constraints-v6)
6. [Variance Annotations (V6)](#variance-annotations-v6)
7. [Utility Types (V6)](#utility-types-v6)
8. [PHP 8.4/8.5 Features](#php-844-85-features)
9. [Property and Method Annotations](#property-and-method-annotations)
10. [Assertion Annotations](#assertion-annotations)
11. [Suppression Annotations](#suppression-annotations)
12. [Advanced Features](#advanced-features)

---

## Basic Annotations

### Property and Constant Type Annotations (@var)

The `@var` annotation specifies types for **class properties and constants**. It cannot be used on local variables.

```php
class Example {
    /** @var string */
    private $name;

    /** @var array<int,string> */
    private $items = [];

    /** @var int */
    const MAX_SIZE = 100;

    /** @var DateTime|null */
    public $timestamp = null;
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQDGA2BDAzsgBAUQB6ILZngFM0BvaNCtAegCoa0ABAN0QCc1kAXVgSwDsA5mhpVylMLxadiAEj55CAbmhiKteszZo2rRAE9g-TgBouvQQD5hoymgk8psntNzoAvGgDaAXWWrqdIws7EbW-rAA9nxcaACyAIIAGgD6AMoAkgBaGGgeAIwADAV+tupBWgAiiNIAKjy4hAA+fACu8PBhtmAtAEbwPLBoMpz1hFx4YLlore3KAL5AA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Inline Variable Type Annotations (@phan-var)

For **local variables**, use inline type annotations with string literals (`'@phan-var UnionType $varName'`). These must appear as standalone statement expressions after the variable is assigned.

```php
function processData() {
    $result = fetchComplexData();
    '@phan-var array<int,User> $result';
    // Phan now knows $result is array<int,User>

    foreach ($result as $id => $user) {
        echo "$id: {$user->getName()}\n";
    }

    // Use @phan-var-force to create variable if it doesn't exist
    '@phan-var-force int $counter';
    $counter = 0;

    // Heredoc syntax also works
    list($status, $data) = fetchResponse();
    <<<'PHAN'
    @phan-var bool $status
    @phan-var array{id:int,name:string} $data
    PHAN;
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQBmCuA7AxgFwJYHtEAIwCdNkBTAZ1IBEBDVKgCgEocBvaHdnAEnzPgBtUOALw5YxVMggBhTAFswfYgA9qtRgG42HAOQABSFUQBaAG5V8Oc-ioBPYOkSoANAFVSxfAD4uPUv1Tamhw4APQhOAAKEIY4iJgA7jgA1nHxpD68Ajjo6Va29o6u7l7QWuywmDxUkjh03JmCVOmc6AAmwt6c8MVMrMHBxJKYOABELa0AXCxdxUaeAObiAHJUssSMAL4AOogjQRwbpcFhOG7EOPrRxmb4RhX4JDiow8hVqOc36FQARorZsNlBK1MGRENpBMocqgyjg9AZruY7pVHg5BJxkJgkO98IEYejMY4PMIcAAGTQwk4ACQ8xGByBwpBsjioSksfFIw3ilSSpBhfChdVItFQ3ScXFaNCoTBEYgkEAASmQwNh3BoYcANdoIpSAIJLbQwy6GUzmHDfTCYPhcIU0bqG+Emix5GzMNoTVFORCrYgTIX4BzzDbiyUw7V6zQbIA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Parameter Annotations (@param)

Annotate function and method parameters:

```php
/**
 * @param string $name User's full name
 * @param int $age User's age in years
 * @param array<string> $tags List of tags
 * @return User
 */
function createUser($name, $age, array $tags) {
    return new User($name, $age, $tags);
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtkBnAFxwEsA7Ac2QBJKCBTZAVSKZwHIjkAzAK4AbIckb4mKNJlwFkVEvSzUW7Tj2TKWVZAE8muIlIzY8hXHl3BSFGgD56JZbwAy5UsgD2fZE+pHUDBwmEgEcSjYOHClYaEFKAGMSck8IhOCsEiY1HAAKBmYAGiUVYossXUdnAEpkAG8UZCbg0PCxJgB3SM588SZiui0BvyJqgG5oAF8gA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Return Type Annotations (@return)

Specify what a function returns:

```php
/**
 * @return int|null Returns user ID or null if not found
 */
function findUserId($email) {
    return $this->db->query("SELECT id FROM users WHERE email = ?", [$email]);
}

/**
 * @return never This function never returns (throws or exits)
 */
function handleFatalError($message) {
    throw new RuntimeException($message);
}

/**
 * @return void
 */
function logMessage($message) {
    error_log($message);
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAICcCmAXArpgHbICWRuAPkfgDa3IBKehRAzsvm9psgJIARZAHteNemQBmyIsNzJJw-EQAmKeLGiTlAY1ylhJSeRUBVbpj4qAFABJsAWwCGpWgEpkAbxTJfOAsTItrgQpGwAtAB8KgBGUQCO+DwAntYARADKAKIAMlkAwgAqZCrIAGKMAPIAspwWHADqABJZjFnIji4MALzIIGkANMgA2vbOrgC6bgDc0AC+0HCI6hj+rDLYAG48yIWhHNpEegYkRFs7a8Qc1iGYwgDuHKIdAB6kuGxu6pqHx4bIECcqlo2DKTlwTloWUwd0wdgc2DYbCcAHNsB5vL5fLcHht7kxlPoEVkXjpsGB9IZ4YjkWiZvNFggkKhVixApthKQ1KgfrpKSRaMIUdUaajsNSkWKMT5fDxYQB9QUoiW09GzOZAA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Property Type Annotations (@property)

Document magic properties on classes:

```php
/**
 * @property string $name
 * @property int $age
 * @property-read string $id Read-only property
 * @property-write string $password Write-only property
 */
class User {
    private $data = [];

    public function __get($key) {
        return $this->data[$key] ?? null;
    }

    public function __set($key, $value) {
        $this->data[$key] = $value;
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgE4HswFMsAXAT2QGcisBLAOwHNkASWgQwFt8U1NcDiydIs1b0uqDNjyFSAWiz5WAEwpU6jJtRUAlRUtk5aAGzJT+pbpL4ySsgO40i+VTQbMwrcuTs4sKgOqO+AbGptYC3LDQAMZGnuTIAKrkhMgA3ijIWdjUAG6sTsxKBazIALzIANoAugDc0JnZAK4ARkbU0cgAZk200UTUhsgA+sNiRAAUTADW+CQAlOmNWSsKRE1YtMxEENTksgB8xUSslTNz1cggIMi0TUZG9SvIAL4Nz2Ct7Z09fQNDoxSk3OJAANMx8kYmvhFhlns8mDs9odjqcQZcKkxIdCnis3i8gA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

---

## Union Type Syntax

Phan supports a rich set of union type syntax for precise type annotations. Union types allow you to specify that a value can be one of several types.

### Basic Union Types

```php
/**
 * @param int|string $value Can be an integer or string
 * @param int|float|null $number Can be a number or null
 * @return bool|array
 */
function process($value, $number) {
    return is_string($value) ? [$value] : true;
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtkBLAOwBcAfAZzJ1IHNkASANywBsBXAU2QGEsJZACNeg4uW71uOZAHtZNOiXoo0mXAQmUAZuzlZKJTu3bNj+UbIFDRyLMgtX5s46bUYc3Mpxy25cuwUuHgAnmqw0DqcJADGZERyQmA4crHcVFQAFKwcPAA05pyWMgCUyADeKMg1Xj5+xFQA+koMOWxc3OUgyADauZ0AusgAXMi0PADc0AC+QA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Array Types

```php
/**
 * @param array<int,string> $map Array with int keys and string values
 * @param array<string> $list Array with string values (any keys)
 * @param string[] $strings Equivalent to array<int,string>
 * @param non-empty-array<User> $users Non-empty array of User objects
 * @return array<string,mixed>
 */
function processArrays($map, $list, $strings, $users) {
    return ['result' => $map];
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtlc8BPYASwDsAXAGgGdqcqBzAPmQBJ8sxkBBHKWQB3ctQjIq1ZAGsApiXpFKAE2SNmlFsgBuWADYBXefRRpMuAkSFYym1h04HyjAbZKjxkh9r2GTZQAKLEpPBSUASnMMbDxCXxYAbQBdLkTlAFEARyNyfQN5GmRqAHsbUgoaBiZHGMt45EpSygBaeXwwahJW4jtgAFV6eRwnI2GcZQA5FvbO7oq7ZFKAM2QhkeWAIwAreQBjajNUDBx5aiMcSkX7Wu1afHIAD3lVNnNYaBWjSkPyFuQYBwpX2pnoglI9CC3F4tC4LkYcM4GSR4xG9EiyAA3ihkHizhcrsgkgByM70IwGagk5AAXicPDAKQA3NAAL5AA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Array Shapes

Array shapes allow you to specify exact array structures:

```php
/**
 * @param array{name:string,age:int} $person Exact array structure
 * @param array{id:int,email:string,active?:bool} $user Optional 'active' key
 * @return array{success:bool,message:string}
 */
function validateUser($person, $user) {
    return ['success' => true, 'message' => 'Valid'];
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtlc8BPAbwDsCBTALgGcAXHASwoHMAaLdutxgL7IAJGGo56AewrIAogA8sAY0ZEcpZExwBXFdpzUUaTLgJrSZFgBNa-TtXxYWAGwbM2XZYxYA3aiFoAI0lJZyFhbXpxZAB5MG9pLGdkAHIvX2oU5ABrahIjDANGfRliLHJ6XSVqenogkOdOfBr6HjotDwEjWGgAM20KFRZpZB8k6yxGagBVKJwAClFxKQpOEUjxAEpkMhRkfaKS5ABtFMqlatqsgF4APmRmbWo1lObatpv7lIA1casUgC6AG5oAIgA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Callable Types

```php
/**
 * @param callable(int,string):bool $callback Takes int and string, returns bool
 * @param callable():void $noArgs No arguments, no return value
 * @param callable(User):array<string> $transform User to string array
 */
function runCallbacks($callback, $noArgs, $transform) {
    $callback(42, "test");
    $noArgs();
    $transform(new User());
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtkBjLAG1KwCNSBTACgEsA7AFwBoBnFnZgcwEoAXJQD2I0sgAkJcpSxEA1sgAqWBTQ7JmLZFiYATZFx5NebZDhosArjiabR4lGky4CxMhWr0hANxEMhpJMIgCCOLyaAHIiuhHW+DSsHOYhFla2TMi+ZNY0zhjYeIQyXrR0AKocNDhCuHgAnsDGfAB8Utx6HABmIjiEVTXILLEtpnGNzrDQ3dZMRCwMIlk4cwDCnnKKHHTSm-IK5sFhESkdePa9-fzIAN4oyI97sgd0ACwATOYARCwaLN9+ABuB5PELhSJ0YGg85dK74OhMGgAd2QgxwUOhAF8gA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Class String Types

```php
/**
 * @template T
 * @param class-string<T> $className Name of a class
 * @return T Instance of that class
 */
function instantiate($className) {
    return new $className();
}

/**
 * @param class-string $anyClass Any class name
 * @param class-string<Exception> $exceptionClass Only exception class names
 */
function loadClasses($anyClass, $exceptionClass) {
    // ...
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqKa6YRATkbsgMbEDOrAtK5rQJYB2Ac2BkAfMgAkLIuwByDbMnm5FAewBmyIszatKGWtkwBXWv3LIAkv25F+TNZswQSOmXtSxo64-cy9VcwFbfn8SbAAKKV1lbABKZABvFGRUwxMzZH5sAHdJaTkFCLiAbmgAX2g4RH1qOgY3di4eAUFJOwBPAGFdZABBfg7G1iyFWpp6RgLObj4hYABRAA8HMH9A8QlsFew1gP4e92QAeX4CIe3V9fNp0ZUPeC8fP33kAlUiABND9mxWKM6P1YABpJJddtcgQlkqlUrBYMgAHTIipAA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Literal Types

```php
/**
 * @param 'read'|'write'|'execute' $permission Specific string values only
 * @param 1|2|3 $level Specific integer values only
 * @return true Always returns true (not just bool)
 */
function checkPermission($permission, $level) {
    return true;
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtkByHAUywBMiAfIgdxwEsAXUmo0gD1IGMBXVkWQASMKRz5GAZymMA9gDtkAZTE9GAM0Y9kU5kwUBzZADcsAGz6kpyReYCeKNJlwFkARmoAmagGYR5qQmpOYqaprayIwKrIbiphZWNnaOqBhkzHw4SvpWyACC5nRY9jYZWQo2uaTIABQKcszIAFZ8esgARnJy5gCUTrDQGnwKPMzySjwQvADWAArikjITtaKL0rKKADQBQSG9yADeKMin5dnI1QDc0AC+QA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Object-like Arrays

```php
/**
 * @param object{name:string,age:int} $person Object with specific properties
 * @return object{id:int,created_at:string}
 */
function createRecord($person) {
    return (object)['id' => 1, 'created_at' => date('Y-m-d')];
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtkB7AIwCsBTAYwBcBvAOwIoC4BnGnASwYHMAaLL1Y8aAX2QASMBRxsiDZAHly1GsgDuXGhGRsZVLgDMuVZGBxEZOGlwpsUadDgo0ArjkWlKtOlwAmLKL8VC5YNBT+APrh7Jw8vGKOsNBGbgy0XArIoRThFABK1EQ4-gAU0rLyDACUyHQoyE0u7p7IZd5qNQDaAOQBvcgAvAB8yACM-Mi9ufnR4YOjyP75Zb0AmgC0+Jv+vTUAugDc0GJAA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Advanced Union Type Combinations

```php
/**
 * @param int|string|array<string>|null $mixed Complex union
 * @param array<int,string>|array<string,int> $either One array type or another
 * @param callable(string):void|null $optionalCallback Callback or null
 */
function complexTypes($mixed, $either, $optionalCallback) {
    // ...
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtkBLAOwBcAfAZzJ1IHMLc8BPYGuk+gPgpIFcANoOQASfEQAeAUwAmyAMIB7fGEHTJyfiSJKSKNJlwFkzLG1JkANBwa8zbW1yuXuY6UTIRpOZAHkSaVMcVmQyFjAgpV8sEiUvHwMMbDxCAGMsYSwAI3UACid6AEoALgA3JSJZPiERUSUwMl0STIVMwWysNIBrRXbOnuRo5AFhA1hoADNtNKa9ZDSVNQ0AFQjpKjzxKTkrd09vHD36xubW-q7uouQAbxRkB9hYZAA6N+gAXyA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

---

## Multiline Doc Comments (V6)

**New in V6:** Doc comments for `@param`, `@var`, `@return`, and Phan-specific annotations can now span multiple lines. This is useful for complex nested array structures and generic types.

### Basic Multiline Syntax

```php
/**
 * @param array{
 *   user: array{id: int, name: string},
 *   permissions: array<string>,
 *   metadata: array{created_at: string, updated_at: string}
 * } $data Complex nested structure
 */
function processData($data) {
    // $data has complex nested structure with full type checking
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAICcCGAnZeu2AngN4prI4pJ51wCtwALqzgM+kwEsAXXMO1oDdeFkrlyTFi+AGyRGXnMk1ZS1MAFV7YAXwATJFxFI9r2s4qRXQEvOV7mYyRuCJIvEARMwhZANzQNomPIRZMGgBvFGQX6gGx-hVVCJy8YOXV3pAUyrLmltWNzf3+Hs6h0dm5BGOj2BYVjV2lNfXqjAFYW2JH5ZVGjk+zMXgOlVV1BwqLivyYJ1UZ8vH1NyqXVdXZYoMphzLLzxHT5uQXI0EVqHOHmr2v6AKzQ7OPh5+1e6mQ6EFr3K-LiBMhRB8xEj5-9lS7ek3nC0HBM+QyUq6lA=&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Generic Type Multiline Formatting

For complex generic types, you can spread them across lines for readability:

```php
/**
 * @template TKey
 * @template TValue
 * @param array<
 *   TKey,
 *   array{
 *     id: int,
 *     value: TValue,
 *     metadata: array<string, mixed>
 *   }
 * > $items
 * @return array<TKey, TValue>
 */
function transformItems($items) {
    // Process and return transformed data
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCGAnZeu2AngN4prI4pJ51wCtwALqzgM+kwEsAXXMO1oDdeFkrlyTFi+AGyRGXnMk1ZS1MAFV7YAXwATJFxFI9r2s4qRXQEvOV7mYyRuCJIvEARMwhZANzQNomPIRZMGgBvFGQX6gGx-hVVCJy8YOXV3pAUyrLmltWNzf3+Hs6h0dm5BGOj2BYVjV2lNfXqjAFYW2JH5ZVGjk+zMXgOlVV1BwqLivyYJ1UZ8vH1NyqXVdXZYoMphzLLzxHT5uQXI0EVqHOHmr2v6AKzQ7OPh5+1e6mQ6EFr3K-LiBMhRB8xEj5-9lS7ek3nC0HBM+QyUq6lA=&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Phan-Specific Annotations with Multiline

Extended multiline support for Phan-specific annotations:

```php
/**
 * @phan-param array{
 *   users: array<int, User>,
 *   roles: array<string, Role>
 * } $config
 *
 * @phan-var list<array{
 *   id: positive-int,
 *   name: non-empty-string,
 *   active: bool
 * }> $result
 */
function processUsers($config) {
    // Multiline Phan annotations provide clearer documentation
}
```

This feature helps with:
- **Readability**: Complex types are easier to understand when formatted nicely
- **Maintenance**: Changes to nested structures are clearer
- **Documentation**: Self-documenting code through formatted type definitions

---

## Generic Type Annotations (V6)

Phan V6 introduces comprehensive support for generic types across classes, interfaces, and traits.

### Generic Classes

Define reusable classes with type parameters:

```php
/**
 * @template T
 */
class Container {
    /** @var T */
    private $value;

    /** @param T $value */
    public function __construct($value) {
        $this->value = $value;
    }

    /** @return T */
    public function get() {
        return $this->value;
    }
}

// Usage
/** @var Container<string> $stringContainer */
$stringContainer = new Container("hello");
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqK8s0AxsQM73IDCA9gHaZECW72ATsgDeKZGIRp0ANyKCyqamLFh+3GaQAkMggFdsAbmijxiDGFlFc5ZFqK7sC48jA6ARgW41kAMx3samNwcyAD6ITQc9Jj8OgEAFLb2AJTCTko2mBDc9AC0AHzaesgAvDaFBk4AvkbpEhj82Jg6-OzWVE4u7p4+fgFBrQDmjXEpIunpDU0tGVm5BXZ6hunV1XCwyACq9ERDcKbSsiwcXLwCwFGq7AN5Nhe8A2ycPHyC7Rp3V48nLyXIfADuRyep34cQARBBsAQCKwwUl9EA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Generic Interfaces (V6)

Define generic interfaces and implement them with specific types:

```php
/**
 * @template T
 */
interface Repository {
    /** @param int $id */
    /** @return T|null */
    public function find($id);

    /** @param T $entity */
    public function save($entity);
}

/**
 * @implements Repository<User>
 */
class UserRepository implements Repository {
    public function find($id) {
        // Returns User|null
        return $this->db->findUser($id);
    }

    public function save($entity) {
        // $entity is User
        $this->db->saveUser($entity);
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqK8s0AlgHY4BOAZkQMbbIBK2YA9gGcamPowCeyAN4pkshGnRgijIrmT1MyACQ0AJqmqy5iDI2yYArozrkAPnQsECBmbLAWARgRptkzC3RsmDR8Nsz0ugAUOroAlADc0K7I8hhKKmpk2tgMwhJUye5ePn4BQSE2AkQAbtjROcGYYgnQAL5J8pQYNPgEeA0C3LyCwqJiwACqAtiMAHyU1GzEAoNTMzz8QiLi6r39DIMbI9sS0kbIRd6+-oHBoX4R0XqxUsnnsLBDltar04z2jgIbyMZm+Ni0mAgNAEAFpZroPHDwnRdGtGE84olzu1Cp4rqVbhVkFVavVck0XmdzkYPtlyRJocg0cDZBCobD4YjZiTsGiyY1mlijO1WkA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Generic Traits (V6)

Define generic traits and use them with specific types:

```php
/**
 * @template T
 */
trait Timestampable {
    /** @var T */
    private $timestamp;

    /** @param T $time */
    public function setTimestamp($time) {
        $this->timestamp = $time;
    }

    /** @return T */
    public function getTimestamp() {
        return $this->timestamp;
    }
}

/**
 * @use Timestampable<int>
 */
class UnixTimestampedEntity {
    use Timestampable;
}

/**
 * @use Timestampable<DateTime>
 */
class DateTimeEntity {
    use Timestampable;
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqK8s0mATkQJabkO7YDOmR+RARgdmQBvFMjEI06AG5Fa5VNTFiwtBjNIASTKw5d8AbmijxiDGFnd5WnQuPIwAV34MAxsgBmDgHYvtAey9kdmxMMh1ObjAACms2AEphOyVkLQgGdgBaAD5tNgj8ZABeFNzsQ2SAXyNkiQxaEIdaQLJbZMdnN08ff0CAcxCwvL1ohJFk5PrMRsDU9OzS-LBypSqquERKDAdgliHIvgFgBi9MLMpqF2J2dmQAVS8GAA9B3UjsABMAUROmAE9E5LbQQvRYHMrQNYSTboIG7V48fjYYAAERI2BeZ1slyI12QqJwL2+2kw-zGSlhIOGYMMFSAA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Multiple Template Parameters

```php
/**
 * @template TKey
 * @template TValue
 */
class Map {
    /** @var array<TKey,TValue> */
    private $items = [];

    /**
     * @param TKey $key
     * @param TValue $value
     */
    public function set($key, $value) {
        $this->items[$key] = $value;
    }

    /**
     * @param TKey $key
     * @return TValue|null
     */
    public function get($key) {
        return $this->items[$key] ?? null;
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqA0tgJ4ppZ6EnbkBqRBArtnbNAMbEAzkOQBZImGQBvFMnkJ6ANyIAnZGtVFqwSjQA0Zdl2wA+VH3nywqgJYrSAEls5cogLzIA2gF0A3NByCohB8vRgakS45FTUyI4A1jShqBgRWtFGHNzxKiYp8JZWYJwARgS2-MgAZpwAdvyYtgD2dchC2JgAFIkGudnYAJQyKVaOmBC2QgC0pi54Ql691D7Ino553AFWyAC+gTuKBWmRmbHxSbQ7qeiqnZyqbVkmAD51nAQEBUXWZRVVtQaTVayAA5p0epdhrJrlY7pgHm1xpMZnNXItlqsQCBkO9PtsrPtdkA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Generic Function Templates

```php
/**
 * @template T
 * @param array<T> $items
 * @param callable(T):bool $predicate
 * @return array<T>
 */
function filter(array $items, callable $predicate) {
    return array_filter($items, $predicate);
}

/**
 * @template TIn
 * @template TOut
 * @param array<TIn> $items
 * @param callable(TIn):TOut $mapper
 * @return array<TOut>
 */
function map(array $items, callable $mapper) {
    return array_map($mapper, $items);
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqKa6YRATkbsnfQJ7BkB8yAJAJY64AzpQw16jAMZECxAEYFsACjIBKAFyyA9poI8wtbABNeUnCPQHMAV1oA7JrVbsOlWNABmV2xMy9N9914CHFpFZiIWHn48QQAaZCkZInlsPQNjU2wVZABvFGQCyxt7cJYAfUDg7FC+ATi0oxMSLIBuaABfaDhEcwFCZvIASVtevH7SMgB5K0xzMQYHJzJhrlqYuboFxLkFZWH1KZmeXCIwMGrzIrtFiPZpzBdUN09vX39kE7AwxwiouvjtskFMdTudaNk8gVCthrNdSmVPopuJ8wfE1kIVG12kA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

---

## Template Constraints (V6)

Template constraints restrict template parameters to specific types or their subtypes:

### Basic Constraints

```php
/**
 * @template T of Animal
 */
class AnimalShelter {
    /** @var array<T> */
    private $animals = [];

    /** @param T $animal */
    public function add($animal) {
        $this->animals[] = $animal;
        $animal->feed(); // Safe: all Animals have feed()
    }
}

// Valid usage
/** @var AnimalShelter<Dog> $dogShelter */
$dogShelter = new AnimalShelter(); // Dog extends Animal

// Invalid usage - Phan will report an error
/** @var AnimalShelter<Car> $carShelter */
$carShelter = new AnimalShelter(); // Car does not extend Animal
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqyA9gGbICCAdgJa5EEryzQDGxAzn3rNWBAMoRsBHACdkAbxTIlCNOgBuRWZulEAnsDIA+VFyVKw0phtIASIsLaCAvMgDaAXQDc0RcsQYwTSJccmQ7BwITX2QwAFcAIwImbmRqWIZuTCZKBmQiABN8gApwljYASnloszDMCCY+AFpDezKCPg9kF1KRbxqanrZm6mxsYvLPZFhYZFEiEYAuPIJIxjbBCCI1bFTR8eiAX2gjuBmANTYmfORYviIAc2w4f3VNITbxSRlgABFKe+MNny-0+Umwsk40CBIIkYNkLgY2AA7u8RKCZEUJlMZn97shsAAPHAMfKCNYiHzTZAASQYGiS11uDx2jWQAAVNrkkUwVshpNgwJRpJg8rlwdIhc9VBpZOS2OjwcAAMKaQHcTQKiFcGzq6SarrIREouViWEYrFUlWyYHYQQMSgiwnE64moA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Interface Constraints

```php
/**
 * @template T of JsonSerializable
 */
class JsonCollection {
    /** @var array<T> */
    private $items;

    /** @param array<T> $items */
    public function __construct(array $items) {
        $this->items = $items;
    }

    public function toJson() {
        return json_encode($this->items); // Safe: all items are JsonSerializable
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqyA9gGbIBSAzpQHYDK2ATgJZEFcBeRAEYFsKeLGgBjYgwb0mzAMKUCoqZi4tkAbxTIDCNOgBuRDsnMciAT2BkAfKkkGDYbmdIASLjlwMAbmh9Q0QMMHMiXEsOaztHZB8-eQkQ5DAAVxEuKWRqDOYNLWZkAH1SqRYGTA4MjQAKK1tE3zwGAEpdNNdEzAguBgBaB1b-ZABeFuSgnoBfYJ7M7Nz8ws1tTEpGFnrOvR6ejmxMDI4SgCtFUuxCygATbHqvPoHh0Y6A5FhYZFYiamwAC5LGpkO8YtgFCx2NxeAJhKI0vNZkA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Multiple Constraints

```php
/**
 * @template T of ArrayAccess&Countable
 */
class CollectionWrapper {
    /** @var T */
    private $collection;

    /** @param T $collection */
    public function __construct($collection) {
        $this->collection = $collection;
    }

    public function isEmpty() {
        return count($this->collection) === 0; // Safe: T is Countable
    }

    public function first() {
        return $this->collection[0]; // Safe: T is ArrayAccess
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqyA9gGbICCATg0QJ50DG72AztwGQBhSgFcAdpiIAjAthTxY0dsV7IhBGe0wBLSqIDqzMGGwNkAbxTIrCNOgBuRUxXmWrYBloekAJO0rrsTR1RAG5oV2QbDDBHIlxyZF9-DW1dVAUrN2FpLXZkajEgtIB9Yr9RbkwGYU0ACiSAotEASnMIzMTMCC1uAFoAPj9G1NFkAF5EoZTgsI6AX3COsGyCXPzCkeQegFF8TBZa1osOjoZsTGEGUb8xTHqunoGpwJHWsffkAAYQyNhkAGUiNRsAAuBI9VQicRSGQRBYRZY5PIFURNfJaBiVQ5tE6ZM4XK6dbp9QbJF7BADanwAuj9YH9AcCwRQIYxmGxODxuHDoHMgA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

---

## Variance Annotations (V6)

Variance annotations control how generic types relate to each other in inheritance hierarchies.

### Covariant Templates

Covariant templates allow subtype substitution in output positions (return types):

```php
/**
 * @template-covariant T
 */
interface Producer {
    /** @return T */
    public function produce();
}

/**
 * @implements Producer<Animal>
 */
class AnimalProducer implements Producer {
    public function produce() {
        return new Animal();
    }
}

/**
 * @implements Producer<Dog>
 */
class DogProducer implements Producer {
    public function produce() {
        return new Dog(); // Dog is subtype of Animal
    }
}

// Valid: DogProducer is subtype of Producer<Animal>
/** @param Producer<Animal> $producer */
function acceptProducer($producer) {
    $animal = $producer->produce();
}

acceptProducer(new DogProducer()); // OK with covariance
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY4C0AxgPYBuRATgJZEB2myAKivLNA69nQBmRctmQAFOpQAmAV1F1kAbxTI1CNOjrZMsusw6oeatWFkAjAg3LJBs5uUwNKBsFLmiAFAEoA3NABfaDhELgwGfAI8bFYAZwl3eQFgAEFmCKICAD4uHnJiWPi0jIJJGSTFCMJouITyhWVVUwsrGzsHJxdkN3rsH0aTQeRtXX1kZmwAd2Ri3Eyff0GgoJCkVHDImsx4so9kgBFKAHMco2h8okLkQ6PdiuQqqNwY7bq9xRVBs0trW3tHZyuRJebwDIZqEZ6AwTaY3BbIWCwa7HB7xWIWTAATzAYkoghm6TmBCayGWwURyAAapkGNIAFzI27AgSo5Do8xYnHIPFvCqpQmZHIaDBgehEXC8hT8kpZZAAEh67zO7QBXREojAmDuCk8CuZdFBnxMcpYJWQAF55YqKqQstaQf4VursJrtQJPDDGW66D4-AikQB5ADSyEmDEwEGQVFojBYoiAA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Contravariant Templates

Contravariant templates allow supertype substitution in input positions (parameters):

```php
/**
 * @template-contravariant T
 */
interface Consumer {
    /** @param T $item */
    public function consume($item);
}

/**
 * @implements Consumer<Animal>
 */
class AnimalConsumer implements Consumer {
    public function consume($item) {
        $item->feed();
    }
}

/**
 * @implements Consumer<Dog>
 */
class DogConsumer implements Consumer {
    public function consume($item) {
        $item->bark();
    }
}

// Valid: AnimalConsumer can consume Dogs (Animals can consume any subtype)
/** @param Consumer<Dog> $consumer */
function processDogs($consumer) {
    $consumer->consume(new Dog());
}

processDogs(new AnimalConsumer()); // OK with contravariance
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY4C0AxgPYB2mATkQG5F0CWRtyAKivLNK1rY6AMyLlsyAMI0AzgFdcw5AG8UyDQjTowLIrm7IAJKxwG+6jWHkAjAq3LIR86uUysayKtQVKAFCZmAJQA3NAAvtBwiLwYrPgEeNi0stJyisLAAILU8UQEAHy8-OTEsqk5eQQyPhl0yPGESSlptUr1ahpWtvaOzq7unt6+2AGmeEGqll0agXikBSLY2AAmfqHTkZHRSKhxCc2YqTUjdMAAIpQA5kWoJWWpl1cndQ0HSi0v7VMz1nYOThcbg81C86X8c1wk06My6kIWNhYAGt1mEZlsorBYMgAGr5VgrABcyEquHyX2U5A4YLakieqT8pPyqSpoOGGWQHAAnsgFDZMFywNggjsMLoGAYKWcngVjOzvhZ+sDPGA6JQJOV6QF5cJodMjDq6AsdX5qNgAO7IJ7rDbbVXq7Ca66yU0Wkm5MnVcHCG0hZBY5AAeQA0shzaYIDT6EwWOxXNggA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Invariant Templates (Default)

Without variance annotations, templates are invariant (exact type match required):

```php
/**
 * @template T (invariant by default)
 */
class Box {
    /** @var T */
    private $item;

    /** @param T $item */
    public function set($item) {
        $this->item = $item;
    }

    /** @return T */
    public function get() {
        return $this->item;
    }
}

/** @param Box<Animal> $box */
function useBox($box) {
    // ...
}

useBox(new Box()); // Must be exactly Box<Animal>, not Box<Dog>
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqyAFAJYB2AbkQE7VG2bIBGAnsgCbYAZkQCuBTAEoU8WNADGxAM6LkAIQD2AD2QBvFMgMI06Rk3KpZBg2BaNSAEmo5cAbmj7DiDGGZFc5x2cLD2QwEU4CajlkQRFaOUxqdVpkRWxMSkC8CV0Qq2R7TAhqRQBaAD4nPGQAXgKq1xCAX3d8owwmdJEmFIoZELCIqJi4hKSUgHN0yhy9fPzOzG6UwuKyyuc3fJaWuC90HyY-NS1gAEFaalwiAnKCzi1g2PjE5OQRNI1NTIfNWZDYLBkAA6UHQXYfbBfSi0bAAdxO3wkEhcyEByAAsh8OJxsMhsJoiAkCLwvudLtdbgAaZC0dQcMkAEXUE3KQA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

---

## Utility Types (V6)

Phan V6 introduces several utility types for more precise type annotations.

### key-of and value-of

Extract key or value types from array type expressions:

```php
/**
 * @return key-of<array{foo:int,bar:string}> Returns 'foo'|'bar'
 */
function getValidKey() {
    return 'foo'; // OK
}

/**
 * @return value-of<array{foo:int,bar:string}> Returns int|string
 */
function getValidValue() {
    return 42; // OK - int is valid
}

/**
 * @param key-of<array<string,int>> $key String type
 * @param value-of<array<string,int>> $value Int type
 */
function processMapping($key, $value) {
    // $key is string, $value is int
}

// Invalid examples that Phan will catch:

/**
 * @return key-of<array{foo:int,bar:string}>
 */
function invalidKey() {
    return 'baz'; // Error: 'baz' is not a valid key
}

/**
 * @return value-of<array{foo:int,bar:string}>
 */
function invalidValue() {
    return false; // Error: bool is not a valid value type
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAICcCmAXArpgHbIDW2AngLQD2AZsAIaaaMUDedNNAXAJZFcAGgBGzHgGdcmAQHMAvgD5kAJTyEiE5AHIuNbQB9tYzNpTxY0OviIBjXHxolZeAGqMANnwAmAaUoAFACUyOwoyBE4BMQ6etoA3MiwsMgA8r7Q8tBwiOYYURrIAG6e+Ni0DMysHHr8gqLiUjJECspq0ZrIArgGTXLmltZ2Dk7ILrjuXt6TZcGh4ZHqMQAsAEyJyWm+yFRdgl1aJVOZ2QhIqBhgzIwAtmSUFUwsbMB9LULdisoAJOQUyABlaRyZC4ChgbB5dBXVh3I5lR5VF5vWQfQRfZDfeHYZAASX2YIhAysNnsjhIYEwNFs2AkEgAsowwGA5AFfpQhJjsSEwhEIpt2f8+FoUZysaUccK9rgTnAUvijj5kNgAB63MAeWmgiCMXDIAAKOpIAHc+B4PMhbLrbBAeKdchcsEsSH9Ec8atw6sITJJgS0lMShmTRgJFX5AjyFsgCjFjIwAF4JJIpACiLBomB4OjEiYOyCIND1jGKniVf1lZyhMZI2Ld1U4nu6DUzKIDqEGpJGJFDpemErmvL51eQdE8EmwG1T6czyBE3AtUoLRZLUxXZVB4Mh8iAA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### int-range

Specify integer ranges:

```php
/**
 * @param int-range<1,12> $month Month number (1-12)
 * @param int-range<1,31> $day Day of month (1-31)
 * @param int-range<1900,2100> $year
 * @return string
 */
function formatDate($month, $day, $year) {
    return sprintf("%04d-%02d-%02d", $year, $month, $day);
}

formatDate(12, 25, 2024); // OK
formatDate(13, 25, 2024); // Phan error: 13 is outside range 1-12

/**
 * @param int-range<0,100> $percentage
 */
function setOpacity($percentage) {
    // $percentage is guaranteed to be 0-100
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtkBLAOwBcBaPEgcwFNgBGAGkYCYA+ZAEnwHtyEZAFkBZISQCu+AEZ0cyABSMK7AJQo0mXAWLkqWWgxYBmRl24ATLAE9kAEVvI+AM2T9BSlWY2oM2PEJSSmp6JgBOAAZI5jZGaIsbOlxNDBw6MkkcEmQAZzIcUhpNWGgXSRIAYzIiAWQXPhx8LDJHMjpFXjEIZh5rG17uJNw1ZABvFGQp9MzsvLBC8hdFACIAUkiAFksKDbYdvcsVweGcQY9xQf61AG5oAF9oMsbm1paO9l62AFYvyLZNrdkLBYMgAPIAaWeTRabQ+Ji+v2QbH+gJuwNBAAUIIZkPIcI0AFzIRgmYi5ZySMi5IiWOjIUL0lTsJ4IJB+bSBPQhQxhGLxSIWMDySp0chYeglMoVaq1HK5DJg7CVIhkGydYU4UXi+ijCZTKYgnia7VkCX0ogUmiSHTkOh0SzIMh8ZByZCRVTRB5AA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### positive-int and negative-int

```php
/**
 * @param positive-int $count Must be > 0
 * @return array<int>
 */
function generateSequence($count) {
    return range(1, $count);
}

generateSequence(5); // OK
generateSequence(0); // Phan error: 0 is not positive
generateSequence(-1); // Phan error: -1 is not positive

/**
 * @param negative-int $debt Amount owed (negative value)
 */
function recordDebt($debt) {
    // $debt is guaranteed to be < 0
}

recordDebt(-100); // OK
recordDebt(100); // Phan error: 100 is not negative
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtkwB7AZwEsAXcgNwFMBacgO0uQBIBjYgV1eQCyPUmwBGdZAD5kABhRp0OOpR45myXHgCewFpUnzY0AGZ9O1YuoDmdZnTyU6AZToBHHrc50AFF16sASmQAbxRkcKUVNWQ8ZhtvAEYAGg5uPkoAgG5oAF9oaBs7B2c3D2YvbwBWLORYWGQAeQBpAtt7LEcXd08fGRq65AAFCCx1exxiHAAuWWRyUmRmYjYSCmp6VqKOku7ynwYE-vrh0eRxyZmDuYWllbIqWjp8hCRUDGw8QjsrDsemfnYABM6KI2ABBfD+NjEADudEByG831+9GQNCwABsPAFDCYzBZ1EpuDhAQAREGUXzA0FBULhcIDIEU67IKw8XCjRzw5CUYjIcTIYCyXL5ImTMkU7wHGR9TK1erNaBiknk0GJGVHIYjMY4CbTZAJGUs26LOg-dZ0IA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### non-empty-string

A string that is not the empty string `''`. This includes the string `'0'` (which is falsey in PHP but non-empty). Phan infers this type when a variable is compared with `!== ''`.

```php
/**
 * @param non-empty-string $name Cannot be empty string
 */
function greetUser($name) {
    echo "Hello, $name!";
}

greetUser("Alice"); // OK
greetUser("0");     // OK - '0' is non-empty
greetUser(""); // Phan error: empty string not allowed
```

### non-falsy-string

A string that is truthy in PHP — excludes both `''` and `'0'`. This is stricter than `non-empty-string`. Phan infers this type when a string passes a truthiness check such as `if ($s)`.

```php
/**
 * @param non-falsy-string $code Cannot be empty or '0'
 */
function processCode($code) {
    echo "Processing: $code";
}

processCode("ABC"); // OK
processCode("");    // Phan error: '' is not non-falsy-string
processCode("0");   // Phan error: '0' is not non-falsy-string
```

The two types relate as follows:

| Type | Excludes `''` | Excludes `'0'` | Inferred from |
|------|:---:|:---:|---|
| `non-empty-string` | Yes | No | `$s !== ''` |
| `non-falsy-string` | Yes | Yes | `if ($s)`, `!empty($s)` |

### Combining Utility Types

```php
/**
 * @var array{
 *   users: array<positive-int,non-empty-string>,
 *   status: 'active'|'inactive'|'pending',
 *   priority: int-range<1,5>
 * }
 */
$config = [
    'users' => [1 => 'Alice', 2 => 'Bob'],
    'status' => 'active',
    'priority' => 3
];

/**
 * @param key-of<$config> $key
 * @return value-of<$config>
 */
function getConfig($key) {
    global $config;
    return $config[$key];
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIDcCGAnZeu2AngN4prLICuAzgKa60BcBuRxwYA9rQJYAXPpnoBaPgDsBAGgncJo+gFswA4qNoDckgOYA+aRSrJN2AXVYBybAGMhIywB9Lk2-fpPLYehIAmuy0NUYzBtbm01VkkBUSIJHXpgAEZpAFY9IwBfClhoABIbeQAzPh1kAF5kAG0UY0s6RlpLCr1qpJbkSwBBABs+Gw9pZAAmDssAIW4AI0sAXSC603MmsbdhQdqqLzCI4mby1oBmaFmAbmg4RCN0MDxsJWQAa3p1biLgAuLS1rzn4mvcPRzLgJMgcD1qGI3h9ChISvoctAitQJHY+PJkAkBABhL46AAUvxeAEpkORjJietNsD1kJ84aVzhTAcDQfT4VUicQztBMkA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

---

## PHP 8.4/8.5 Features

Phan V6 adds full support for PHP 8.4 and 8.5 language features with type annotations.

### #[Deprecated] Attribute

Mark functions, methods, and class constants as deprecated using the attribute:

```php
#[Deprecated(message: "Use newMethod() instead", since: "2.0.0")]
public function oldMethod() {
    return $this->newMethod();
}

#[Deprecated]
class LegacyClass {
    #[Deprecated(message: "Use VALUE_NEW instead")]
    public const VALUE_OLD = 1;
}
```

Phan will emit `PhanDeprecatedFunction`, `PhanDeprecatedMethod`, and `PhanDeprecatedClassConstant` when deprecated items are used.

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAICcCGAnZeu2AngN4prLICuwGYBmAdgJYBOZAJygE5kAXAGQBnKhQBsaARgG1mgYIaDg8BPC8FsNVm0BhMzCAZsFNMNv86zyAMZkGFJFWQBvFMkoA+VEhNm-RIyy+TKmHSCZAAZZBGIeNKG8AO6SCEsVnwsvAC+yBmV2aVlZRVVVQZGxnAJmgBsJM0qDLOQfGyMKlwyOKphfmDkz10ARLYxqhDhKvEqAIzI5HKVHCFZKRVVUJmI4gGOVJ8AIL6FmDQqaggZKJ8X6jnRH2ZKz1klJSnlqcS1V95eKvJk5AAWpqhYxWjZhJwLFm9VzcbqBpXCiRQSvl1PLCVX6tTEknHBbXj9MRV5qUJKRdmVYAr1FqlVIqBHlSo8h+k5N48BVDfmXHl5cHD9bCVA2pGMz+b6oIzHQxYfgSWy65M85f9bZ2qqLrH2RrHfkVfBCvWQSqBYr5AOVZjJCPQKfZ1U0gfAhG1m+VZDaQPblmRnZxI0Yc03B4h06jxYKjMyDJPvplMzpvB-TAkXvXGlZlN+RX5lfn1+dWVMsJqBOl1Nldu7Tl3YJKhHvvfgO52Fw3j+nKkwZdYcJ+zl+XnsR4PW50A&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Typed Class Constants (PHP 8.3)

Declare types for class constants with full inheritance checking:

```php
interface Config {
    public const int PORT = 8080;
    public const string HOST = 'localhost';
}

class ApiConfig implements Config {
    public const int PORT = 3000;  // OK: compatible type
    public const string HOST = '0.0.0.0';  // OK: compatible type
}

class BadConfig implements Config {
    public const string PORT = '3000';  // Error: type mismatch
}
```

Phan validates:
- Type compatibility in inheritance (`PhanConstantTypeMismatchInheritance`)
- Assignment types (`PhanTypeMismatchDeclaredConstant`)
- Never type violations (`PhanTypeMismatchDeclaredConstantNever`)

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAICcCGAnZeu2AngN4prI4pJ51wCtwALqzgM+kwEsAXXMO1oDdeFkrlyTFi+AGyRGXnMk1ZS1MAFV7YAXwATJFxFI9r2s4qRXQEvOV7mYyRuCJIvEARMwhZANzQNomPIRZMGgBvFGQX6gGx-hVVCJy8YOXV3pAUyrLmltWNzf3+Hs6h0dm5BGOj2BYVjV2lNfXqjAFYW2JH5ZVGjk+zMXgOlVV1BwqLivyYJ1UZ8vH1NyqXVdXZYoMphzLLzxHT5uQXI0EVqHOHmr2v6AKzQ7OPh5+1e6mQ6EFr3K-LiBMhRB8xEj5-9lS7ek3nC0HBM+QyUq6lA=&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Property Hooks (PHP 8.4)

Full support for property hooks with type validation:

```php
class User {
    /**
     * @var non-empty-string
     */
    public string $username {
        set {
            if (strlen($value) === 0) {
                throw new ValueError("Username cannot be empty");
            }
            $this->username = $value;
        }
    }

    /**
     * @var positive-int
     */
    public int $age {
        get {
            return $this->age;
        }
        set {
            if ($value < 0) {
                throw new ValueError("Age must be positive");
            }
            $this->age = $value;
        }
    }
}
```

Phan detects:
- Hook parameter type mismatches (`PhanPropertyHookIncompatibleParamType`)
- Return type incompatibilities (`PhanPropertyHookIncompatibleReturnType`)
- Set hooks on readonly properties (`PhanReadonlyPropertyHasSetHook`)
- Default values on hooked properties (`PhanPropertyHookWithDefaultValue`)

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQDGA2BDAzsgBAVWQUwE5oG9o0S0B6AKguNLQrQAEA3RfAOwHs2BabAWzAAXAJ7dkg3AEs2AcxqkKZeWjABXAEbxJsNOKmy0AElU5cbRH2yFltHIOu1HpSQDM0ACj3xsbd4ZbwqtgAlGgAvBFoAAyhRE7xpIIQuBwA7mhs2OkAaoiB2ACiuCm47gBEWHjmlmiwiGyc9upW-ELCZcEA3DbxAL49ToZJksjcAHwmVRZWYUYBQd0J-bT9ypTUjvTMrCocyJKCkkzY3NKCNorKapraaGdGiDJWcfF2DgnObn7zVsCzMe8Po4kil0pkcnkgkUSuUAIJPNB8ExNKxgPYHI7YDqLIFoZa4oYQEbjR4zOaQ7A4pz4kj9XpAA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### #[NoDiscard] Attribute (PHP 8.5)

Mark return values that shouldn't be ignored:

```php
#[NoDiscard]
function generateId(): string {
    return uniqid('id_');
}

#[NoDiscard(message: "The transaction was not committed")]
function startTransaction(): Transaction {
    return new Transaction();
}

// Using (void) cast to suppress the warning
(void) generateId();  // OK - explicit discard

$id = generateId();  // OK - value is used

generateId();  // Error: PhanNoDiscardReturnValueIgnored
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQDGA2BDAzsgBAVWQUwE5oG9o0S0B6AKguNLQrQAEA3RfAOwHs2BabAWzAAXAJ7dkg3AEs2AcxqkKZeWjABXAEbxJsNOKmy0AElU5cbRH2yFltHIOu1HpSQDM0ACj3xsbd4ZbwqtgAlGgAvBFoAAyhRE7xpIIQuBwA7mhs2OkAaoiB2ACiuCm47gBEWHjmlmiwiGyc9upW-ELCZcEA3DbxAL49ToZJksjcAHwmVRZWYUYBQd0J-bT9ypTUjvTMrCocyJKCkkzY3NKCNorKapraaGdGiDJWcfF2DgnObn7zVsCzMe8Po4kil0pkcnkgkUSuUAIJPNB8ExNKxgPYHI7YDqLIFoZa4oYQEbjR4zOaQ7A4pz4kj9XpAA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Pipe Operator (PHP 8.5)

Full type inference through pipe operator chains:

```php
/**
 * @param positive-int $value
 * @return positive-int
 */
function double($value) {
    return $value * 2;
}

/**
 * @param int $value
 * @return string
 */
function stringify($value) {
    return (string) $value;
}

// Phan understands the full type chain
$result = 5 |> double(...) |> stringify(...);
// $result is string
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtl8BLADwFMATZAEgDcsAbAV3JTXR3IBdmcA7ZACMA9iMbsMkLPwC0WAM4LyObrO45WsgMYj+lYt2J7kCjcX4BzWgxZtUsaADNm-bUZPEFAZXNWAFPRMrACUyADeKMjRXLwCyF4A+mY4FpaBtqEA3NAAvtDOru7GghAylIzkgZRY3FhhkdHRxE7I-l6+qQE0NXUhDVFNTbCwyAAKZYIA1vwiAO4KtL1YCYspacgQKvZDTeTaECKmGpX81bX1OUP5uUA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

---

## Property and Method Annotations

### Magic Method Annotations (@method)

Document magic methods that don't exist in the source code:

```php
/**
 * @method string getName() Get the user's name
 * @method void setName(string $name) Set the user's name
 * @method static User create(array $data) Create a new user
 * @method int|null findIdByEmail(string $email)
 */
class User {
    private $attributes = [];

    public function __call($method, $args) {
        // Magic method handling
    }

    public static function __callStatic($method, $args) {
        // Static magic method handling
    }
}

$user = new User();
$user->setName("Alice"); // Phan knows this method exists
$name = $user->getName(); // Phan knows this returns string
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIFsCmAXCA9gCbIDOuATgJYB2A5snXgHICGOAFAJTIDieyfNmQBXUtgoByUshrtsKNFjyESANwJUS43G07lq9ZABI5OHgGUBQ0eKkyzC1Bhz5iZXK1xUAxsgCqdsg+FNhe2BysFBSsAJ4mRF6sPADCoeHIrLLYAO62EoouKu60uAA+NCIANlXIAGa0RACSRABCsQCimKxUVRwGtAzG2N29XIqw0D5VrKQygRLIAN4oyGtg1GoZxl6UVABGIrjYMgC8yADaALoA3NCr6yL7Vb71IjQ+3gQ0yAD6vz5WDUOMZXKoADQmKJ0Ug8FZrBEI2CwZAAWVYdFeYPcEFYNCIL3oD2QAF97oiwE8Xn5yF5XnV3p8qN8-gCgVULJ5vD4QdiiJCdhQYXDiYjkchOXS-N1MdLiiRcfjCXRiWSydBjGJFucaLkAnZuHdNXYALQAPh0egiACIAILU7DWrg3ZDigAKiuQAGsaAQcjJ8FQZHzkNgAB5B3CkDWOZDnY0Sc1MXTyQ2ulEevHe33+wQQIPIUK4EQUGgyAb0IA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Property Hooks (PHP 8.4 / V6)

Document property hooks:

```php
class User {
    /**
     * @var non-empty-string
     */
    public string $username {
        set {
            if (strlen($value) === 0) {
                throw new ValueError("Username cannot be empty");
            }
            $this->username = $value;
        }
    }

    /**
     * @var positive-int
     */
    public int $age {
        set {
            if ($value <= 0) {
                throw new ValueError("Age must be positive");
            }
            $this->age = $value;
        }
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQDGA2BDAzsgBAVWQUwE5oG9o0S0B6AKguNLQrQAEA3RfAOwHs2BabAWzAAXAJ7dkg3AEs2AcxqkKZeWjABXAEbxJsNOKmy0AElU5cbRH2yFltHIOu1HpSQDM0ACj3xsbd4ZbwqtgAlGgAvBFoAAyhRE7xpIIQuBwA7mhs2OkAaoiB2ACiuCm47gBEWHjmlmiwiGyc9upW-ELCZcEA3DbxAL49ToZJksjcAHwmVRZWYUYBQd0J-bT9ypTUjvTMrCocyJKCkkzY3NKCNorKapraaGdGiDJWcfF2DgnObn7zVsCzMe8Po4kil0pkcnkgkUSuUAIJPNB8ExNKxgPYHI7YDqLIFoZa4oYQEbjR4zOaQ7A4pz4kj9XpAA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Readonly Properties

```php
class User {
    /**
     * @var non-empty-string
     * @readonly
     */
    public string $id;

    public function __construct(string $id) {
        $this->id = $id;
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQDGA2BDAzsgBAVWQUwE5oG9o0S0B6AKguNLQrQAEA3RfAOwHs2BabAWzAAXAJ7dkg3AEs2Acxql6DXNkQATLvGHySFMtrABXAEbxJsNOKmy0AEkmqA3NH3HT5gGYG2sQZK5oAfQDYLksDHwAKS2kZW3sASkJtWhtBCElkbgA+ezQAXjjHbQBfaGKgA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

---

## Assertion Annotations

Phan supports assertion annotations to help with type narrowing and validation.

### @phan-assert

Assert types for parameters:

```php
/**
 * @param mixed $value
 * @phan-assert string $value
 */
function assertString($value) {
    if (!is_string($value)) {
        throw new TypeError("Expected string");
    }
}

function process($input) {
    assertString($input);
    // Phan now knows $input is a string
    echo strlen($input);
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtl8BLADwFMATZAEgDcsAbAV3JTUwiwDsBaLAM4DyOAC7IBonMW4BzWgxZtUsaADNm3AMajiAe27JBwsQGUpM2QAp6TVgEpkAbxTI3xNcisBCYgID6ktJyNooOji5uUVGiEDh6AO7I3ORJACoAnmDkAKI48ThWAEQ5pNk6VBIWckX2ANyubgC+0C3qmjr6hmDxWuRCNjJgzKIRjUZCIqLmwdY0QyP147CwyAAKXIbcicgA1tsJArQL4n5GVbPj5FoQeheM5NyD3MOjDU1AA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### @phan-assert-true-condition and @phan-assert-false-condition

```php
/**
 * @param mixed $value
 * @return bool
 * @phan-assert-true-condition string $value
 */
function isString($value) {
    return is_string($value);
}

function handle($data) {
    if (isString($data)) {
        // Phan knows $data is string here
        echo strlen($data);
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtl8BLADwFMATZAEgDcsAbAV3JTXR3IBdmcA7ZACMA9iMbsMkLPwC0WAM4LyObrO45WsgMYj+lYt2J7kCjcX4BzWgxZtUsaADNm-bUZPEFAZXNWAFPRMrACUyADeKMjRXLwCyF4A+mY4FpaBtqEA3NAAvtDOru7GghAylIzkgZRY3FhhkdHRxE7I-l6+qQE0NXUhDVFNTbCwyAAKZYIA1vwiAO4KtL1YCYspacgQKvZDTeTaECKmGpX81bX1OUP5uUA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### @phan-assert with Generics

```php
/**
 * @template T
 * @param mixed $value
 * @param class-string<T> $className
 * @return T
 * @phan-assert T $value
 */
function assertInstanceOf($value, $className) {
    if (!$value instanceof $className) {
        throw new TypeError("Expected instance of $className");
    }
    return $value;
}

function processUser($input) {
    $user = assertInstanceOf($input, User::class);
    // Phan knows $user is User
    echo $user->getName();
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqKa6YRATkbsrgJYAe2AJsgCQBuRBAK7ZKGGvUYBjYgGcZAWhmZazAHYBzYGQB8PaUTkA5BiNQZa2TINqryo6hCKr5BmdlqZyPfkNPxY0ABmgqqSmMwA9rau7pgAkqpKTpLYAPKBABR8AsIANHqyMsa42ACUyADeKMg1zIHIGQCE2b7Iakmh2BH13PpGJuVVNcPDmBC0EQDuyKrY02QAnmDYAKK0E7QZAEQrrMthnG2JmMnYyN0FrsXYW6UA3NU1AL6PyBZWNt452A8vQSFhSK2MATFJyACqbk23DUYEEmEGr24gihyAAvMgYh4Eh0UuksrD4flIe4AFykvoye6vWCwZAABUctgA1qopjIeCj3G0OSTaK9sJIIBFOVD5Np1JZrhlqU8gA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

---

## Suppression Annotations

### @suppress

Suppress specific issue types:

```php
/**
 * @suppress PhanTypeInvalidThrowsIsInterface
 */
function riskyOperation() {
    throw new Exception();
}

class Example {
    /**
     * @suppress PhanUnreferencedPublicMethod
     */
    public function unusedMethod() {
        // Method is intentionally unused
    }
}

// Inline suppression
/** @suppress PhanTypeMismatchArgument */
processValue($wrongType);
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIGcCuYwCcBTTTZABQgEMA7AFQE8xCBJagN0oBsBLAE1oj4A9gHdMzcdQAuhfADNKAY0Ip4saHOzVFU7kOrJ83TAGt6AeSb5Ku-QAoAlMgDeKZO6mDRyaoRHIAUQAPZTBbakcAbmgAX2hoRU5KEkCgygBbME5CFzd3BCR3IrQsXAJiUgoaAFVqIjlZQm1CXjJsACMeRQBZQk8hXjz3NSGwDq7kTW1w5C1sTBbe-t5HXKL15FhYZCWIAeRjA+km8K5OelnqeZahuLi4bdYeX2QcPCISPWo4RAw38pSVTojEI3WM6RsiggAEF8ABzbDpE6odQEITKEgANS42EIdgAJCJhNQ4QwmA5IkA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### @phan-suppress-next-line

Suppress issues on the following line only:

```php
/** @phan-suppress-next-line PhanTypeMismatchArgument */
processValue($wrongType);

/** @phan-suppress-next-line PhanTypeArraySuspicious, PhanTypeMismatchReturn */
return $complexValue;
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU8AEABSBDAdgWgM4FcwwAnAU11201IA8AXbAGwEtqkAFCLAFQE8xSAWWa4AtujoBjCAEFiAc3yjSmOknixoJAPaTyuAGrpG+UgAoAJAHdi2zPL4CAlAG5ocRKgw4CRMhSpaBhY2Th5+UjlidF4AZXxcMGZJZm0EgBoOLkxHIRFxKQgAJVI6fGJMdU0yMoqkC0ltUTBGWiMTUhcgA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### @phan-file-suppress

Suppress issues for an entire file:

```php
<?php
/**
 * @phan-file-suppress PhanUnreferencedClass
 * @phan-file-suppress PhanUnreferencedPublicMethod
 */

// All classes and methods in this file won't trigger these issues
class HelperClass {
    public function helperMethod() {
        // ...
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAKQIYDsC0AZgJYA2ApngM4CuYYATmZZcgAoS4CqOjBZjOAMZkAJgGESWZijSYO+YuSq0GTFuy48yfAcJGtqAIxJFBAWTIAXCAHsRM2NDixkAQRIlkgycybJcIsgAtla2IixEOMjWRCyKZMgA7jY4AOSW0fREAObZ-NEQTAmxNEzQ3lIsABJkJGD8EpXIAN4oyO1gRiaCyATUQpZEKciFdfwW1nYAFACULW3ti8iwLgB06wvIAL7QW0A&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

---

## Debugging Type Inference

### @phan-debug-var

When Phan infers an unexpected type for a variable, you can ask it to print the inferred type by adding an inline string expression statement containing `@phan-debug-var`:

```php
function example(array $items): void {
    $first = $items[0] ?? null;
    '@phan-debug-var $first';
    // Phan emits: PhanDebugAnnotation - $first has union type mixed
}
```

This must be a string **expression statement** — not a comment — because php-ast does not expose comments to Phan. Phan emits a `PhanDebugAnnotation` diagnostic with the inferred union type.

You can debug multiple variables at once, including array elements and properties:

```php
'@phan-debug-var $arr[\'key\'], $obj->prop, $result';
```

This is useful when:

- Investigating why Phan reports a type mismatch you don't expect
- Verifying that type narrowing in a conditional branch works correctly
- Understanding how a complex union type is flowing through your code

> **Note:** Remove `@phan-debug-var` statements before committing — they intentionally emit diagnostics every time Phan runs.

---

## Advanced Features

### @deprecated

Mark deprecated code:

```php
/**
 * @deprecated Use newMethod() instead
 */
function oldMethod() {
    return $this->newMethod();
}

/**
 * @deprecated 2.0.0 Will be removed in 3.0.0
 */
class LegacyClass {
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBMCmYBO2AxgIYAu2myAqgM7bIB22A7gLLakQD2mAFAJTIAlgxrlimFPFjQAZgFcGhUkK4NkXADaZ2nHgOQBvFMlMFS8vOoAknITQC0APiZsO3PvwDc0AL7Q4RCkMHHwiMgpkACYAOgAGeOQAdSFNTWQAI3oCAFsuADdIkWQAZnj4qRlCTWIaGmQAGWwAc2JCAE8AYRq6oz8gA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### @internal

Mark internal APIs:

```php
/**
 * @internal This class is for internal use only
 */
class InternalHelper {
}

class PublicAPI {
    /**
     * @internal
     */
    public function internalMethod() {
        // Not part of public API
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIEsB2AXApgE7YCGANsgCoSYDOyAxmSbfXcgGYD2hyOBxcsgCutfMi7YyATxTxY0Ji3oBJPEVJkAEvjJgiyAN7QAvtEXNWyAArCARmUwMAgtZVGUyLwiRe-aLHVBMk9-BT9kMHtHBk5hbAZcTEk+IM0AWXxcCC4AEwAKAEoPCIjYWGQAOS5cSJJCWq4OSOinZFcVUOQzEyA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### @override

Indicate method overrides (PHP 8.3+):

```php
class Parent {
    public function process() {
    }
}

class Child extends Parent {
    /**
     * @override
     */
    public function process() {
        // Override parent method
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQDGA2BDAzsgBABUQJwKYDsAXNAb2jQrTAFcAjeAS1jQDNr9ZCGB7fK7brFyoAFAEpS5SgF9osuElRoAwhAbwAJmlwAPQgQ3oseIpMpoA9ACorUylbQABbgDdc2bAw247FKxd8aeiZWdk4ePjABIVEJMnNzCws0AHk3Dy9cKhwCYgBbXEIIbg1fWWkgA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### @throws

Document thrown exceptions:

```php
/**
 * @param string $filename
 * @throws FileNotFoundException When file doesn't exist
 * @throws PermissionDeniedException When file isn't readable
 * @return string
 */
function readFile($filename) {
    if (!file_exists($filename)) {
        throw new FileNotFoundException($filename);
    }
    if (!is_readable($filename)) {
        throw new PermissionDeniedException($filename);
    }
    return file_get_contents($filename);
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtkBnAFxwEsA7Ac2QBIAzcgGwFNKDWU10SIcA9gHciyAGItWAOQEkxAgK6UAJgFEAHgGNWYEuQGVkAdQjtkTNsmUDWRSgHISyVuvKluGPoJHIACqxx8NyJ9SgARdnJWNS0dPQNjU0MLVmQ3ByccVixlLAAjNg90LJIFHENSChpuWGgGJU14wyyciTYACkZJDnxWAEpkAG8UZFHyBmR2gEIUgH0XNxIiTpSe-oHh0a2tr2FkSlYhcUkZOUUVDW1dUJXuzj6AbhHRgF9ntInpt1mW3ILWW5sNZ9Dbvba7I4HI7+QLBUIRShRGJXJqA9j3J7bN7bEplZKSWbUVgkWaaAwkdhLNHAp4vIA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### @mixin

Indicate trait or class mixing:

```php
/**
 * @mixin Builder
 */
class Model {
    use HasBuilder;

    // Phan understands Model has Builder methods
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIFsCWAPbAdsgEICu2ANgCYCmATivLNAMYUCGAzp8gLID2tCsgDeKZBNKcayABJcylWnQDc0cRNixkABQjsipAss4AXA1R4ChyfT0XV6yTDVMRBnaAF8gA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### @phan-type

Define type aliases:

```php
/**
 * @phan-type UserId = positive-int
 * @phan-type UserData = array{id:UserId,name:non-empty-string,email:string}
 * @phan-type UserCollection = array<UserId,UserData>
 */
class UserRepository {
    /**
     * @param UserId $id
     * @return UserData|null
     */
    public function find($id) {
        // ...
    }

    /**
     * @return UserCollection
     */
    public function findAll() {
        // ...
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAKQIYDsC0ALgJ5gCmyAqgM6kBOAkgCbIC8yYA9lQJYHcBupPNxwEUaTBFyES5anQAiWAllbIstWliIBvbowBc8howA0OLAFtSBnB3ylLYYnioFaIgOanHWbgBsDNw8cTwBfcQxsfGIyShpaAGEOf39SAGM+ezUNLSJgYyZTYyUVAD5xWGh0-ywqKni6ACVSTh4CDloiZB0UZH6EJH7hiTANK0aTZAASfT6RjFpSAgBXWhxJ0qwAHxwV1Pn++Crh9hWAI39udOQAMxWcTO5s25FGAApZxgBKHsPT2CwZAAOlBhwih0G-wkS1W60myVSGSyOGhJ2GYAuVxu90eKLubwAgql3r9eqcAUDQcDwdAwkA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### @phan-pure

Mark pure functions (no side effects):

```php
/**
 * @phan-pure
 * @param int $a
 * @param int $b
 * @return int
 */
function add($a, $b) {
    return $a + $b;
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAKQIYDsC0YArgE4CmKamWxWAtsgJY4AuyAJFhRmNXYy+wBGXdGWYkc-ZhVjQAZoRwBjZgwD2krABMtACg4AaIQEpkAbxTIrYieyzIA1EIDc0AL5A&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### @phan-immutable

Mark immutable classes:

```php
/**
 * @phan-immutable
 */
class Point {
    public function __construct(
        public readonly int $x,
        public readonly int $y
    ) {}

    public function withX(int $x): self {
        return new self($x, $this->y);
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAKQIYDsC0AlgLZECuALlgEYA2ApivLNAMY1YDOHyACgPYEc5ZAG8UyCWFK0CLZADNSOFuQJ8cyAPqaW6juQBOpFQApxEi8iky5BulgAm6mgE9kg4QBIAHgBpzllbSNLLIdo7Obh7Ini4BAJSiAL7QAdYhcorKqurIAO4E5BAAGibRPvEAXMgcdDTyogGWduSkBho4dHk1dfImPr4xRQQceAB8LvEA3AEpSUA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Template Type Aliases with @phan-type

```php
/**
 * @template T
 * @phan-type Result<T> = array{success:bool,data:T|null,error:string|null}
 */
class ApiClient {
    /**
     * @template TResponse
     * @param string $url
     * @return Result<TResponse>
     */
    public function get($url) {
        // ...
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqKa6kRAdgLSYCeY2yAStgM4CuBmwMgD5kAXmREATpKJMA3rwDGi7lwBcAIwD2WggBoAJiSJqyAH1p992aVslqumSQEtaAcwtWAvpVjRFxFxcyACCYM4AwgTO2LSYyHIoyMkISMnpVDj4xKRknFxgWrRc2EkZGGBSRLjIji7uyAAkPJIEZclUktiYLbQc3HwCedyFxdhC7ah+6chgPBrRisgAZjy0ipjORchu3QAUza0AlAmT6bCwyAB0N+0+XkA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

---

## Best Practices

### 1. Be Specific with Types

```php
// Bad: Too generic
/** @param array $users */

// Good: Specific structure
/** @param array<int,User> $users */

// Better: Even more specific
/** @param non-empty-array<positive-int,User> $users */
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0sAIBCBDAJgLgQFQPa4QHMBTAO2ICcBLAYzgCp6EABMFClAWwXY4E8EAEgCuAZ0qiE9WNDiIA4vkwIAymGI0qAM1oJRAFwrCa+4RWIMmrdlx4V+wKqX0AaAKriKAPiFiJUmTlkYn19SiwAUQA3MgROXHM9dU0dOlhGFjYOblJcUgBaYk4wfT583hQ+YDBcUSp9Khj8p1cPSh8RT0lpIA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### 2. Use Template Constraints

```php
// Bad: No constraints
/**
 * @template T
 */
class Repository {
    /** @param T $entity */
    public function save($entity) {
        $entity->getId(); // Unsafe!
    }
}

// Good: With constraints
/**
 * @template T of Entity
 */
class Repository {
    /** @param T $entity */
    public function save($entity) {
        $entity->getId(); // Safe: all Entities have getId()
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0sAIBCBDAJgLgQOQPYIGNcA7AZwBcAnFAS2PNLgConoEmEABcgUwFswAGxS8EAFTZNY0AsNKkEAJR5hcpGuVyUAnggDebBEdgsuYFNT7iEAEh70NuqYaNgArgCNBNAggBmbsQE5DQkCKQoAG48ABR2DuTaAJT6Lkbp8SGJALQAfADmPOQAkmgxSQDcCPAIAKpkKH48AIRpAL7QHXCIAOK4uJgIAOoaEIQkFNR0DMys7Fy8AsKiYgi4fggAognaktKyKPJKKmoaWroG6dWmnOaW1pmO7NJX7l4+-oHBocThUbGPRIpS5XDL2LLaPKFEplSrVRAAZUaPCwKEEgi2CRoPAUEH+CGhpXK7U6QA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### 3. Document Complex Return Types

```php
/**
 * @return array{
 *   user: User,
 *   permissions: array<string>,
 *   metadata: array{created_at:string,updated_at:string}
 * }
 */
function getUserData($id) {
    // Complex structured return
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAICcCmAXArpgHbICGmmpAngN4prLL4DO2mAXMgKquYA09RsjBsAtgEtmzcQHsizTuUpVgzXJnFEA5gD4BqIaLykAJqVylFFajQDGOc9hMB9c+zUbtffGDO4nrrju6ppaAL6CEaiw0ABm+ES2uLIkWng8bAAi5qQAFAAk4iYAlMh0QsiwsMgAwjKiYAA22AAeyB74SYROyDgExNBhQA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### 4. Use Variance Appropriately

```php
// Producers: use covariant
/**
 * @template-covariant T
 */
interface Source {
    /** @return T */
    public function get();
}

// Consumers: use contravariant
/**
 * @template-contravariant T
 */
interface Sink {
    /** @param T $item */
    public function put($item);
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0sAIAKAnA9gEwK4GMCmKAzgFwJaF4I5oBuAhigJZ0B2ALnAFSfQKcIABNngC2YADZ1hAWmr0mrNggAqvTrGiN2BAGZ18CAMposKAwG9eCa7G6CUeNqZYq+G69bBYARuMY4EHSwWHDZGNBcAc0cACgBKAG5oAF9oOEQAYQjCLBECEjIKKgi2FDp5ZnYuHj5BYTFJGWp2MorFFTUNLWEUPQNDLQBrBEsPBFt+ATAGOhFXABJGercrTx8-AKCQsIiELzYYxfrElKA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### 5. Combine Utility Types

```php
/**
 * @param array{
 *   port: int-range<1,65535>,
 *   host: non-empty-string,
 *   timeout: positive-int
 * } $config
 */
function connectToServer(array $config) {
    // Type-safe configuration
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtlc8BPAbxTWWTAHscAXALmQEsA7BgWj3YHMApsACMAGgBsAVkkBmSQD5Rlasgi0Azs2Tta7LgPxgGJLppwc+S1Coat8A2gFctdda1sA3AVw4NlAX2QAEgBjXQAzVj5KWGhwx3YQ211kMPZ2ASSAFVoAZQEcLxwACmIsEmC0yL4ASmQKFWRYWGQskjBvdSxwgVSIqMc8ZPZofyA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

---

## Common Patterns

### Builder Pattern with Generics

```php
/**
 * @template T
 */
class Builder {
    /** @var array<string,mixed> */
    private $data = [];

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @param class-string<T> $className
     * @return T
     */
    public function build($className) {
        return new $className($this->data);
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqK8s0AxsQM73IBCArgJYEAm2ATsgG8UyEQjToAbkX7TeRAJ7B6mXuwB2AcwA0udgA9sXAHypqIkWFVTSAEi4kiyALzIA2gF0A3NGGjEvkXEwaSJcZGVVTWQbAGtseQDUDGC5MN0DLmipAlZsRPFebExWXjVozAh2enyzczBWACMCdhpkADNWNRpMdgB7MvoigApY+K0sohzsAEpBRPMbCqqAWiN7TCJXUfl3Zwmp73Mj5ELi0vLK+kPzAF8fI7F85JCwuiJGZYj1DWAyExs3owAHKhPLHApFEplCjg2oWRrNVodLo9frIBocbgjQH0EG4GZzY7mU5Q5BqbAAd2iOLx2BGS3oq3WRGm1xEdxuQA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Repository Pattern with Generics

```php
/**
 * @template T of Entity
 */
abstract class AbstractRepository {
    /**
     * @param positive-int $id
     * @return T|null
     */
    abstract public function find($id);

    /**
     * @return array<positive-int,T>
     */
    abstract public function findAll();

    /**
     * @param T $entity
     */
    abstract public function save($entity);
}

/**
 * @extends AbstractRepository<User>
 */
class UserRepository extends AbstractRepository {
    public function find($id) {
        // Returns User|null
    }

    public function findAll() {
        // Returns array<positive-int,User>
    }

    public function save($entity) {
        // $entity is User
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqyA9gGbICiAdpgJaYCeK8s0RARgM6YATkQDGmZKOL9+yAIIDhYzACVsYSv1aUhbZAG8UyYwiTHzadGCIjcyDVpYA3bAFpmTZABJmAEyMWGELYmACuQgzkAD4MoQQEAcZciXyCIuL2obwEzKLI1KEM4syUkdQevgAUPr4AlADc0ImmiahBIeGRNiJswA6szC7uTAA0ZAB8rcnmyKlKGWBZOXkFRSyl+RVy8ZUNTTMtM21WNkR2FF7YTKwcR9Pmc+kSi9m5+YXFG-xELtVXLOw9gBfJqHSzYAAeOAYvlkCjSyjU-UwOl6AFV+NghJNUNwpEQZMgMVikZptLpkJDobD5IonqTHKiDIkXst3msSmUKtU-LVmUdzLBYMg1GEIrJiUIYnEEjMQSylm9Vp8uTDtgRdvyBcghSKOuLZkIen0yc43B5MCNJTjzPKZqylR91pFvr9LtdAVqBbr3QC9MwJZihIkQUCgA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Factory Pattern with Generics

```php
/**
 * @template T
 */
interface Factory {
    /**
     * @param array<string,mixed> $config
     * @return T
     */
    public function create(array $config);
}

/**
 * @implements Factory<Database>
 */
class DatabaseFactory implements Factory {
    public function create(array $config) {
        return new Database($config);
    }
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAIBcCmBbMANgIY7IAqK8s0AlgHY4BOAZkQMbbIBi7mA9owCeyAN4pkEhEgky06MEUZFcyRUsHAAzpkb0A5gBpcNAB7YAJgD5kAEjZ86zGnvGyMjbJgCujOuVcSVAFgXgBGBDRsyMxedGyYNA7IbB4k2AAUakTCdg5OegCUANzQAL7QcIiUGDT4BHjYDJrcvAIaACIkRKFEmtiWlNRsxJrNnZjdvdg88W3ItYQNTS2zQqLBYRFRMXEJSSnYaZmM6rb2js4F6zI3yB7evsh02ADuyOOTfem5F4UlN+VSkA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

---

## See Also

- [Generic Types V6](Generic-Types-V6.md) - Detailed guide on generics
- [Issue Types Caught by Phan](https://github.com/phan/phan/wiki/Issue-Types-Caught-by-Phan)
- [Phan Configuration](https://github.com/phan/phan/wiki/Phan-Config-Settings)

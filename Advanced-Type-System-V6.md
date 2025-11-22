# Advanced Type System Improvements in Phan V6

Phan v6 introduces significant improvements to type inference and type narrowing, making analysis more accurate and useful for complex code patterns.

## Table of Contents

1. [Intersection Types](#intersection-types)
2. [Type Narrowing](#type-narrowing)
3. [Array Type Inference](#array-type-inference)
4. [Generator Type Support](#generator-type-support)
5. [Literal Type Handling](#literal-type-handling)
6. [Union Type Clamping](#union-type-clamping)

---

## Intersection Types

### Intersection Type Basics

Phan v6 improves handling of intersection types (types that must satisfy multiple interfaces/classes simultaneously).

```php
interface Countable {
    public function count(): int;
}

interface Iterator {
    public function next(): void;
}

/**
 * @param Countable&Iterator $collection
 */
function processCollection($collection) {
    $count = $collection->count();      // Safe: Countable methods available
    $collection->next();                 // Safe: Iterator methods available

    // Error: stdClass methods not available
    // $collection->property;
}
```

### Intersection with Unknown Classes

**New in V6**: Improved handling when some classes are unknown.

```php
interface Known {}

// When intersection includes unknown class
/** @param Known&UnknownClass $value */
function process($value) {
    // Phan still validates against Known
    // But can't validate against UnknownClass
}
```

Phan v6 analyzes the **known types** and skips unknown ones, enabling type checking even with incomplete stubs.

```php
catch (FileException&UnknownException $e) {
    // Phan knows about FileException
    $e->getFilePath();  // Type-checked
    // UnknownException methods not validated
}
```

### Intersection Type Errors

```php
interface Reader {
    public function read(): string;
}

interface Writer {
    public function write(string $data): void;
}

class ReadOnlyStream implements Reader {
    public function read(): string { return "data"; }
}

/** @param Reader&Writer $stream */
function processStream($stream) {
    echo $stream->read();
}

// Error: ReadOnlyStream doesn't implement Writer
processStream(new ReadOnlyStream());
```

---

## Type Narrowing

### Enhanced Type Narrowing

V6 improves type narrowing in various contexts.

#### Array Element Checking

```php
/**
 * @param array<string|int> $items
 */
function processItems($items) {
    // Without narrowing - generic error
    foreach ($items as $item) {
        strlen($item);  // Error: int might not support strlen
    }

    // With type checks - narrowed
    foreach ($items as $item) {
        if (is_string($item)) {
            strlen($item);  // OK: Phan knows $item is string
        }
    }
}
```

#### Literal Type Exclusion in `!in_array()`

**New in V6**: Phan now narrows types when excluding literals from known arrays.

```php
/** @param 'read'|'write'|'execute'|null $permission */
function checkPermission($permission) {
    if (!in_array($permission, ['read', 'write', 'execute'], true)) {
        // Phan narrows to null (only possibility left)
        echo $permission;  // string|int can't be accepted
    }
}
```

Limits: Works for arrays up to 50 literal elements.

#### Conditional Type Refinement

**New in V6**: Improved type narrowing with intermediary variables.

```php
function process($value) {
    $isValid = is_string($value);

    if ($isValid) {
        // Phan now understands $value is string here
        echo strlen($value);  // OK
    }
}
```

#### Static Property Type Narrowing

**New in V6**: Type narrowing works correctly with static properties.

```php
class Config {
    public static string|null $host = null;
}

function getHost(): string {
    if (Config::$host === null) {
        Config::$host = 'localhost';
    }

    // Phan knows $host is non-null string here
    return Config::$host;
}
```

### Complex Condition Narrowing

```php
interface Entity {
    public function getId(): int;
}

/**
 * @param mixed $value
 */
function getEntityId($value) {
    // Phan handles instanceof with is_object()
    if (is_object($value) && $value instanceof Entity) {
        return $value->getId();  // Type-safe
    }

    // Or directly
    if ($value instanceof Entity) {
        return $value->getId();
    }
}
```

---

## Array Type Inference

### Array Filter Callbacks

**New in V6**: `array_filter()` recognizes null-stripping callbacks.

```php
/**
 * @param array<string|null> $items
 * @return array<string>  // Null stripped!
 */
function getValidItems($items) {
    // Phan infers: returns array<string>
    // because the callback removes null values
    return array_filter($items);
}

/**
 * @param array<mixed> $items
 * @return array<string>
 */
function getStrings($items) {
    // Explicit callback - type checked
    return array_filter($items, 'is_string');
}
```

### Array Chunk Return Type

**New in V6**: Improved `array_chunk()` inference based on parameters.

```php
/**
 * @param array<int,string> $items
 */
function processChunks($items) {
    // With preserve_keys=false (default)
    $chunks = array_chunk($items, 2);
    // Type: array<int, array<int, string>>
    // Keys reset to 0, 1, 2, ...

    // With preserve_keys=true
    $chunks = array_chunk($items, 2, true);
    // Type: array<int, array<int, string>>
    // Original keys preserved
}
```

### Constant Resolution

**New in V6**: `constant()` return type inference.

```php
define('API_KEY', 'secret123');
define('MAX_RETRIES', 3);

function getConstant(string $name) {
    $value = constant($name);  // mixed (can't determine)
}

function getStaticConstant() {
    // For non-dynamic constants, Phan can infer
    $key = constant('API_KEY');  // string
    $max = constant('MAX_RETRIES');  // int
}
```

### Foreach Iterator Inference

**New in V6**: Improved inference for explicit iterator generics.

```php
/**
 * @template T
 * @implements Iterator<int, T>
 */
class ItemIterator implements Iterator {
    // Implementation
}

/**
 * @param ItemIterator<User> $iterator
 */
function processUsers($iterator) {
    foreach ($iterator as $key => $user) {
        // Phan knows: $key is int, $user is User
        echo $user->getName();
    }
}
```

---

## Generator Type Support

### Generator Return Types

```php
/**
 * @return Generator<int, User>
 */
function getUserGenerator() {
    yield 1 => new User('Alice');
    yield 2 => new User('Bob');
}

function useGenerator() {
    foreach (getUserGenerator() as $id => $user) {
        // Phan knows: $id is int, $user is User
        echo $user->getName();
    }
}
```

### Generator Send and Return

```php
/**
 * @return Generator<int, string, string, array>
 */
function doubleYield() {
    $received = yield 1 => 'first';
    $final = yield 2 => 'second';

    return ['received' => $received, 'final' => $final];
}
```

Type parameters:
- Key type (yield key)
- Value type (yield value)
- Send type (what can be sent back)
- Return type (final return value)

---

## Literal Type Handling

### Literal Type Narrowing

```php
/**
 * @param 'read'|'write'|'execute' $mode
 */
function setFileMode($mode) {
    if ($mode === 'read') {
        // Phan narrows to literal 'read'
        $permissions = 0444;
    } elseif ($mode === 'write') {
        // Literal 'write'
        $permissions = 0644;
    } else {
        // Literal 'execute'
        $permissions = 0755;
    }
}
```

### Enum Constant Values

**New in V6**: Better handling of enum cases as constants.

```php
enum Status: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}

/**
 * @param Status::ACTIVE|Status::INACTIVE $status
 */
function setStatus($status) {
    // Phan knows $status is one of the literal values
    echo $status->value;  // 'active' or 'inactive'
}
```

### Reference Assignment Literal Erasure

**New in V6**: Literal types are erased in reference assignments.

```php
$var1 = 'literal_value';
$var2 =& $var1;

// Before: $var2 has type string with literal 'literal_value'
// After: $var2 has type string (literal erased)
// Because reference could be modified elsewhere
```

This prevents false positives when variables are aliased:

```php
$id = 42;  // literal int
$ref =& $id;
$ref = 99;

// After reference assignment, $id is int (not literal 42)
if ($id === 42) {  // Warning: always false - good!
}
```

---

## Union Type Clamping

### When Union Types Get Too Large

```php
function complexMerge() {
    $result = [];

    // Each iteration potentially adds new types
    foreach ($arrays as $arr) {
        $result = array_merge($result, $arr);
    }

    // Union type could grow exponentially
    // V6 clamps it: int|string|float|bool|null
    // Instead of: thousands of specific types
}
```

### Configuration

```php
// .phan/config.php
return [
    'max_union_type_set_size' => 1024,  // Default
];
```

Impact:
- `512`: Very conservative, may lose precision
- `1024`: Good balance (default)
- `2048`: Better precision, more memory
- `4096+`: Precise but potentially slow

### Type Merging Rules

When union exceeds limit:

```php
// Before clamping
int|float|string|bool|object|array|resource|null

// After clamping (simplified)
int|float|string|mixed
```

Common types stay separate:
- `int` and `float` kept separate
- `string` kept separate
- Less common types merged to `mixed`

---

## Static Class Return Type Resolution

**New in V6**: Improved `static` return type inference.

```php
class Base {
    public function create(): static {
        return new static();
    }
}

class Derived extends Base {
    public function special(): string {
        return "special";
    }
}

/**
 * @template T of Base
 * @param class-string<T> $class
 * @return T
 */
function instantiate($class) {
    $instance = new $class();

    // For Derived: Phan knows instance is Derived
    // Can call special()
    return $instance;
}
```

---

## Mixed Type Improvements

### Mixed with Objects

**New in V6**: Better inference with `mixed` containing objects.

```php
/**
 * @param object|string $value
 */
function process($value) {
    if (is_object($value)) {
        // Phan knows $value is object
        $class = get_class($value);
    }
}
```

### Union Containing Mixed

**New in V6**: Stricter checking for unions with `mixed`.

```php
/**
 * @param int|mixed $value  // Effectively just mixed
 */
function process($value) {
    // Phan treats as mixed (mixed dominates)
}
```

---

## Array Shape Type Narrowing

### Nested Array Refinement

**New in V6**: Improved type narrowing for nested array shapes.

```php
/**
 * @param array{
 *   user: array{id: int, name: string},
 *   status: string
 * } $data
 */
function refineData($data) {
    if (isset($data['user']['id'])) {
        // Phan refines: $data['user']['id'] is int
        $id = $data['user']['id'];
    }
}
```

### Field Check Narrowing

```php
/**
 * @param array{id?: int, name: string} $record
 */
function processRecord($record) {
    if (array_key_exists('id', $record)) {
        // Phan narrows: $record['id'] is int
        echo $record['id'] + 1;
    }
}
```

---

## Type System Strictness Levels

### Very Strict

```php
/**
 * @strict
 * @param array $items  // Error: specify element type
 */
function process($items) {
}
```

### Selective Strictness

```php
/**
 * Suppress specific type warnings
 * @suppress PhanTypeMismatchArgument
 */
function legacyFunction($value) {
    // Type checks not enforced
}
```

---

## Common Type System Patterns

### Pattern 1: Type Guard Functions

```php
/**
 * @template T
 * @param mixed $value
 * @param class-string<T> $type
 * @return T
 * @throws TypeError
 */
function assertType($value, $type) {
    if (!$value instanceof $type) {
        throw new TypeError("Expected $type");
    }
    return $value;
}

// Usage
$user = assertType($data, User::class);
// Phan knows: $user is User
```

### Pattern 2: Covariant Return Types

```php
interface Service {
    public function create(): Entity;
}

class SpecialService implements Service {
    public function create(): SpecialEntity {
        // OK: SpecialEntity extends Entity
        return new SpecialEntity();
    }
}
```

### Pattern 3: Type Discriminated Unions

```php
/**
 * @param array{type: 'user', user: User}|array{type: 'admin', admin: Admin} $data
 */
function processData($data) {
    match ($data['type']) {
        'user' => handleUser($data['user']),
        'admin' => handleAdmin($data['admin']),
    };
}
```

---

## See Also

- [[Annotating-Your-Source-Code-V6]] - Annotation reference
- [[Generic-Types-V6]] - Generics and templates
- [[About-Union-Types]] - Union type details
- [[Memory-and-Performance-Optimizations]] - Type system performance

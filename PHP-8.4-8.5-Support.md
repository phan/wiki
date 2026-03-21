# PHP 8.4 and 8.5 Language Features Support

Phan v6 provides **full support** for all PHP 8.4 and PHP 8.5 language features with complete type checking and error detection.

## Table of Contents

1. [PHP 8.4 Features](#php-84-features)
2. [PHP 8.5 Features](#php-85-features)
3. [New Functions and Methods](#new-functions-and-methods)
4. [Type Checking Examples](#type-checking-examples)

---

## PHP 8.4 Features

### Property Hooks

**New feature**: Methods to intercept property access with custom get/set logic.

#### Basic Property Hook

```php
class User {
    public string $name {
        set {
            $this->name = trim($value);
        }
    }
}
```

Phan validates:
- Hook parameter types match property type
- Hook return type matches expected value
- Readonly properties with set hooks (`PhanReadonlyPropertyHasSetHook`)

#### Type-Safe Hooks

```php
class Product {
    public int $price {
        set {
            if ($value < 0) {
                throw new ValueError("Price cannot be negative");
            }
            $this->price = $value;  // Type-checked
        }
        get {
            return $this->price;    // Return type verified
        }
    }
}
```

**Detected errors**:
- `PhanPropertyHookIncompatibleParamType` - Parameter type mismatch
- `PhanPropertyHookIncompatibleReturnType` - Return type mismatch
- `PhanPropertyHookWithDefaultValue` - Invalid default values
- `PhanPropertyHookFinalOverride` - Illegal override of final hook

#### Property Hook Examples

```php
class Temperature {
    private float $celsius;

    public float $fahrenheit {
        get {
            return ($this->celsius * 9/5) + 32;
        }
        set {
            $this->celsius = ($value - 32) * 5/9;
        }
    }

    public function __construct(float $celsius) {
        $this->celsius = $celsius;
    }
}

$temp = new Temperature(20);
echo $temp->fahrenheit;  // 68
$temp->fahrenheit = 86;
echo $temp->celsius;     // ~30
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQDGA2BDAzsgBAVWQUwE5oG9o0S0B6AKguNLQrQAEA3RfAOwHs2BabAWzAAXAJ7dkg3AEs2AcxqkKZeWjABXAEbxJsNOKmy0AElU5cbRH2yFltHIOu1HpSQDM0ACj3xsbd4ZbwqtgAlGgAvBFoAAyhRE7xpIIQuBwA7mhs2OkAaoiB2ACiuCm47gBEWHjmlmiwiGyc9upW-ELCZcEA3DbxAL49ToZJksjcAHwmVRZWYUYBQd0J-bT9ypTUjvTMrCocyJKCkkzY3NKCNorKapraaGdGiDJWcfF2DgnObn7zVsCzMe8Po4kil0pkcnkgkUSuUAIJPNB8ExNKxgPYHI7YDqLIFoZa4oYQEbjR4zOaQ7A4pz4kj9XpAA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### #[Deprecated] Attribute

**New feature**: Mark deprecated functions, methods, and constants.

```php
#[Deprecated(message: "Use newMethod() instead", since: "2.0.0")]
public function oldMethod(): void {
    $this->newMethod();
}

#[Deprecated]
class LegacyClass {
    #[Deprecated(message: "Use NEW_VALUE instead")]
    public const OLD_VALUE = 1;
}
```

**Detection**:
- `PhanDeprecatedFunction` - Using deprecated functions
- `PhanDeprecatedMethod` - Using deprecated methods
- `PhanDeprecatedClassConstant` - Using deprecated constants

```php
// Example detection
oldMethod();  // Warning: PhanDeprecatedFunction
LegacyClass::OLD_VALUE;  // Warning: PhanDeprecatedClassConstant
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAICcCGAnZeu2AngN4prLICuwGYBmAdgJYBOZAJygE5kAXAGQBnKhQBsaARgG1mgYIaDg8BPC8FsNVm0BhMzCAZsFNMNv86zyAMZkGFJFWQBvFMkoA+VEhNm-RIyy+TKmHSCZAAZZBGIeNKG8AO6SCEsVnwsvAC+yBmV2aVlZRVVVQZGxnAJmgBsJM0qDLOQfGyMKlwyOKphfmDkz10ARLYxqhDhKvEqAIzI5HKVHCFZKRVVUJmI4gGOVJ8AIL6FmDQqaggZKJ8X6jnRH2ZKz1klJSnlqcS1V95eKvJk5AAWpqhYxWjZhJwLFm9VzcbqBpXCiRQSvl1PLCVX6tTEknHBbXj9MRV5qUJKRdmVYAr1FqlVIqBHlSo8h+k5N48BVDfmXHl5cHD9bCVA2pGMz+b6oIzHQxYfgSWy65M85f9bZ2qqLrH2RrHfkVfBCvWQSqBYr5AOVZjJCPQKfZ1U0gfAhG1m+VZDaQPblmRnZxI0Yc03B4h06jxYKjMyDJPvplMzpvB-TAkXvXGlZlN+RX5lfn1+dWVMsJqBOl1Nldu7Tl3YJKhHvvfgO52Fw3j+nKkwZdYcJ+zl+XnsR4PW50A&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### Typed Class Constants

**New feature**: Declare types for class constants with inheritance checking.

```php
interface DatabaseConfig {
    public const int PORT = 5432;
    public const string HOST = 'localhost';
}

class PostgresConfig implements DatabaseConfig {
    public const int PORT = 5432;          // OK
    public const string HOST = '127.0.0.1'; // OK
}

class MySQLConfig implements DatabaseConfig {
    public const string PORT = '3306';     // Error: type mismatch!
    public const string HOST = 'localhost'; // OK
}
```

**Detected errors**:
- `PhanTypeMismatchDeclaredConstant` - Value doesn't match declared type
- `PhanConstantTypeMismatchInheritance` - Type doesn't match parent constant
- `PhanTypeMismatchDeclaredConstantNever` - Value contradicts never type

**Type narrowing in conditions**:

```php
if (MyEnum::STATUS === 'ACTIVE') {
    // Phan narrows MyEnum::STATUS to literal 'ACTIVE' here
}
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAICcCGAnZeu2AngN4prI4pJ51wCtwALqzgM+kwEsAXXMO1oDdeFkrlyTFi+AGyRGXnMk1ZS1MAFV7YAXwATJFxFI9r2s4qRXQEvOV7mYyRuCJIvEARMwhZANzQNomPIRZMGgBvFGQX6gGx-hVVCJy8YOXV3pAUyrLmltWNzf3+Hs6h0dm5BGOj2BYVjV2lNfXqjAFYW2JH5ZVGjk+zMXgOlVV1BwqLivyYJ1UZ8vH1NyqXVdXZYoMphzLLzxHT5uQXI0EVqHOHmr2v6AKzQ7OPh5+1e6mQ6EFr3K-LiBMhRB8xEj5-9lS7ek3nC0HBM+QyUq6lA=&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### New Functions and Methods

Phan understands all PHP 8.4 new functions with proper type signatures:

```php
// array_find - find first element matching condition
$found = array_find([1, 2, 3, 4], fn($v) => $v > 2);  // int|null

// array_find_key - find first key matching condition
$key = array_find_key(['a' => 1, 'b' => 2], fn($v) => $v > 1);  // string|int|null

// array_any - check if any element matches
$hasEven = array_any([1, 2, 3], fn($v) => $v % 2 === 0);  // true

// array_all - check if all elements match
$allPositive = array_all([1, 2, 3], fn($v) => $v > 0);  // true

// String trimming functions with locale support
$trimmed = mb_trim("  hello  ");     // "hello"
$left = mb_ltrim("  hello");         // "hello  "
$right = mb_rtrim("hello  ");        // "  hello"
$ucfirst = mb_ucfirst("hello");      // "Hello"
$lcfirst = mb_lcfirst("HELLO");      // "hELLO"
```

**Type checking examples**:

```php
// Type-safe usage
$numbers = [1, 2, 3, 4, 5];

// Phan knows the return types
$doubled = array_map(fn($n) => $n * 2, $numbers);  // array<int|string|mixed>
$even = array_filter($numbers, fn($n) => $n % 2 === 0);  // array

$first = array_find($numbers, fn($n) => $n > 3);  // int|null
if ($first !== null) {
    echo $first;  // Phan knows it's int here
}
```

---

## PHP 8.5 Features

### Pipe Operator (|>)

**New feature**: First-class pipe syntax for function chaining.

```php
// Traditional chaining
$result = strtoupper(trim($input));

// With pipe operator
$result = $input |> trim(...) |> strtoupper(...);
```

Phan provides **full type inference** through pipe chains:

```php
/**
 * @param int $value
 * @return int
 */
function double($value): int {
    return $value * 2;
}

/**
 * @param int $value
 * @return string
 */
function stringify($value): string {
    return (string) $value;
}

// Type flows correctly through the pipe
$result = 5 |> double(...) |> stringify(...);
// Phan infers: $result is string
```

**Error detection**:

```php
function expectsString(string $s): void {}

// Type error caught at pipe level
5 |> double(...) |> expectsString(...);  // Error: int not assignable to string
```

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQD0BU9oAJ7IAJgIYCcsFtl8BLADwFMATZAEgDcsAbAV3JTXR3IBdmcA7ZACMA9iMbsMkLPwC0WAM4LyObrO45WsgMYj+lYt2J7kCjcX4BzWgxZtUsaADNm-bUZPEFAZXNWAFPRMrACUyADeKMjRXLwCyF4A+mY4FpaBtqEA3NAAvtDOru7GghAylIzkgZRY3FhhkdHRxE7I-l6+qQE0NXUhDVFNTbCwyAAKZYIA1vwiAO4KtL1YCYspacgQKvZDTeTaECKmGpX81bX1OUP5uUA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### #[NoDiscard] Attribute

**New feature**: Mark return values that shouldn't be ignored.

```php
#[NoDiscard]
function generateId(): string {
    return uniqid('id_');
}

#[NoDiscard(message: "Session was not stored")]
function createSession(): Session {
    return new Session();
}

// Errors
generateId();  // Error: PhanNoDiscardReturnValueIgnored

// OK
$id = generateId();  // Value is used
(void) generateId();  // Explicit discard with cast
```

**Phan detections**:
- `PhanNoDiscardReturnValueIgnored` - Return value was ignored

[▶](https://phan.github.io/demo/?c=DwfgDgFmBQDGA2BDAzsgBAVWQUwE5oG9o0S0B6AKguNLQrQAEA3RfAOwHs2BabAWzAAXAJ7dkg3AEs2AcxqkKZeWjABXAEbxJsNOKmy0AElU5cbRH2yFltHIOu1HpSQDM0ACj3xsbd4ZbwqtgAlGgAvBFoAAyhRE7xpIIQuBwA7mhs2OkAaoiB2ACiuCm47gBEWHjmlmiwiGyc9upW-ELCZcEA3DbxAL49ToZJksjcAHwmVRZWYUYBQd0J-bT9ypTUjvTMrCocyJKCkkzY3NKCNorKapraaGdGiDJWcfF2DgnObn7zVsCzMe8Po4kil0pkcnkgkUSuUAIJPNB8ExNKxgPYHI7YDqLIFoZa4oYQEbjR4zOaQ7A4pz4kj9XpAA&php=84&phan=v6-dev&ast=1.1.3 "Try this example in Phan-in-Browser")

### (void) Cast

**New feature**: Explicitly suppress `#[NoDiscard]` warnings.

```php
#[NoDiscard]
function getValue(): int {
    return 42;
}

// Error
getValue();  // PhanNoDiscardReturnValueIgnored

// OK
(void) getValue();  // Explicit cast suppresses warning
```

This is clearer than assigning to unused variables:

```php
// Old approach (less clear)
$_ = getValue();

// New approach (explicit intent)
(void) getValue();
```

### Updated Function Signatures

Phan v6 includes updated function signatures for all PHP 8.5 standard library changes:

```php
// New DateTime methods
$timestamp = DateTime::createFromTimestamp(time());
$immutable = DateTimeImmutable::createFromTimestamp(time());

// DOMXPath enhancements
$xpath->registerPHPFunctionNS('ns', 'localName', callable);
```

---

## New Functions and Methods

### Array Functions (PHP 8.4)

```php
// Find first matching element
array_find([1, 2, 3, 4], fn($v) => $v > 2);  // Returns first element or null

// Find first matching key
array_find_key(['a' => 1, 'b' => 2], fn($v) => $v > 1);  // Returns first key or null

// Check if any element matches predicate
array_any([1, 2, 3], fn($v) => $v > 2);  // bool

// Check if all elements match predicate
array_all([1, 2, 3], fn($v) => $v > 0);  // bool
```

### String Functions (PHP 8.4)

```php
// Multibyte string trimming
mb_trim("  hello  ");      // "hello"
mb_ltrim("  hello");       // "hello  "
mb_rtrim("hello  ");       // "  hello"

// Case operations
mb_ucfirst("hello");       // "Hello"
mb_lcfirst("HELLO");       // "hELLO"
```

### Type Checking with New Functions

```php
$items = [1, 2, 3, 4, 5];

// Phan understands return types
if (array_any($items, fn($i) => $i > 3)) {
    // We know at least one item > 3
    $first = array_find($items, fn($i) => $i > 3);
    // Phan knows $first is int (even though it could be null)
}
```

---

## Type Checking Examples

### Example 1: Complete Property Hook Flow

```php
class BankAccount {
    private float $balance = 0;

    public float $availableBalance {
        get {
            return $this->balance;
        }
        set {
            if ($value < 0) {
                throw new ValueError("Balance cannot be negative");
            }
            $this->balance = $value;
        }
    }

    public function __construct(float $initialBalance) {
        if ($initialBalance < 0) {
            throw new ValueError("Initial balance cannot be negative");
        }
        $this->balance = $initialBalance;
    }
}

// Type-safe usage
$account = new BankAccount(1000);
$account->availableBalance = 500;  // Type checked
echo $account->availableBalance;    // float

$account->availableBalance = "invalid";  // Error: string not assignable to float
```

### Example 2: Deprecated Code Detection

```php
#[Deprecated(message: "Use TokenFactory::create() instead")]
function legacyCreateToken(): Token {
    return TokenFactory::create();
}

class Authentication {
    public function login(string $user): void {
        $token = legacyCreateToken();  // Warning: deprecated function
        $this->validateToken($token);
    }
}
```

### Example 3: Complex Pipe Expression

```php
function parseJson(string $json): mixed {
    return json_decode($json, true);
}

function validateData(array $data): array {
    return $data;  // type check
}

function formatOutput(array $data): string {
    return json_encode($data);
}

// Full type-safe pipe
$result = '{"key":"value"}'
    |> parseJson(...)
    |> validateData(...)
    |> formatOutput(...);

// Phan infers: $result is string
```

---

## Version Requirements

To analyze PHP 8.4/8.5 code, ensure:

1. **Phan v6.0+** installed
2. **PHP 8.1+** to run Phan (target version can be different)
3. **php-ast 1.1.3+** for PHP 8.4+ syntax support

```bash
# Check requirements
phan --version
php --version
php -r "echo phpversion('ast');"

# Configure for PHP 8.5 analysis
// .phan/config.php
return [
    'target_php_version' => '8.5',
    // ... other settings
];
```

---

## Further Reading

- [[Annotating-Your-Source-Code-V6]] - Full annotation guide with examples
- [[Migrating-to-Phan-V6]] - Migration from older Phan versions
- [PHP 8.4 Announcement](https://www.php.net/releases/8.4/en.php)
- [PHP 8.5 Announcement](https://www.php.net/releases/8.5/en.php)

# Phan Issue Types Reference

Complete reference of all major Phan issue types, organized by category, with examples and how to fix each one.

**Total Issue Types**: 600+

## Quick Navigation

- [Syntax Errors](#syntax-errors)
- [Undefined Elements](#undefined-elements)
- [Type Mismatches](#type-mismatches)
- [Type Inference](#type-inference)
- [Access Control](#access-control)
- [Object/Class Issues](#objectclass-issues)
- [Deprecated](#deprecated)
- [Generic/Template Issues](#generictemplate-issues)
- [Plugin-Specific](#plugin-specific)

## Syntax Errors

### PhanSyntaxError

Code has invalid PHP syntax.

**Example**:
```php
function foo( {  // Missing parameter name
    echo "test";
}
```

**Fix**: Correct the syntax error. Phan shows the exact location and nature of the error.

### PhanSyntaxReturnValueInVoid

Function declared as returning `void` but contains a `return` with a value.

**Example**:
```php
/**
 * @return void
 */
function logMessage($msg) {
    return "logged";  // Error: void functions can't return values
}
```

**Fix**: Either remove the return value or change the return type.

```php
/**
 * @return void
 */
function logMessage($msg) {
    echo $msg;  // OK: no return value
}
```

### PhanSyntaxReturnStatementInNever

Function declared with `never` return type but has a `return` statement.

**Example**:
```php
/**
 * @return never
 */
function throwError($msg): never {
    return;  // Error: never functions must not return
}
```

**Fix**: Remove the return or throw an exception.

```php
/**
 * @return never
 */
function throwError($msg): never {
    throw new Exception($msg);  // OK: never functions throw or exit
}
```

### PhanContinueOrBreakNotInLoop

`break` or `continue` used outside a loop.

**Example**:
```php
if ($x > 5) {
    break;  // Error: not in a loop
}
```

**Fix**: Only use `break`/`continue` inside `for`, `while`, `foreach`, or `switch`.

### PhanContinueOrBreakTooManyLevels

`break` or `continue` targets more levels than available.

**Example**:
```php
for ($i = 0; $i < 3; $i++) {
    break 3;  // Error: only 1 level of loop exists
}
```

**Fix**: Reduce the level number.

```php
for ($i = 0; $i < 3; $i++) {
    break;  // OK: default is 1
}
```

### PhanInvalidConstantExpression

A constant expression contains invalid syntax.

**Example**:
```php
const MAX = $someVariable;  // Error: constants can't reference variables
```

**Fix**: Use only literal values in constant expressions.

```php
const MAX = 100;  // OK: literal value
```

### PhanPropertyHookWithDefaultValue

A virtual property is declared with a default value. A hooked property is virtual when none of its hooks uses its backing value: no hook reads or writes `$this->name` for a property `$name`, no hook is declared without a body, and no `set` hook has the short form `set => expr`. A virtual property has no storage, so PHP rejects the default (`Cannot specify default value for virtual hooked property`). A property that overrides a backed property of a parent class keeps that backing value, and can have a default.

**Example** (PHP 8.4):
```php
class User {
    private string $first = '';

    public string $name = "default" {  // Error: no hook uses $this->name
        get => $this->first;
        set { $this->first = $value; }
    }
}
```

**Fix**: Remove the default value, or use the backing value in a hook.

```php
class User {
    public string $name = "default" {  // OK: the get hook uses the backing value
        get => strtoupper($this->name);
    }
}
```

### PhanInvalidWriteToNullsafeChain

A write to a nullsafe chain: `unset()`, an assignment, a compound assignment, an increment or decrement, a `list()` element or a `foreach` target whose expression contains the `?->` operator anywhere. PHP refuses to compile this (`Can't use nullsafe operator in write context`), and `php -l` does not catch it.

**Example**:
```php
function reset(?Config $config, string $key) {
    unset($config?->options[$key]);  // Error: Can't use nullsafe operator in write context
    $config?->modified = true;       // Error: same
}
```

**Fix**: Check for null, then use the regular `->` operator.

```php
function reset(?Config $config, string $key) {
    if ($config === null) {
        return;
    }
    unset($config->options[$key]);  // OK
    $config->modified = true;       // OK
}
```

### PhanInvalidReferenceToNullsafeChain

A reference to a nullsafe chain: the right-hand side of `=&` contains the `?->` operator anywhere, including a bare nullsafe call. PHP refuses to compile this (`Cannot take reference of a nullsafe chain`), and `php -l` does not catch it.

**Example**:
```php
function options(?Config $config) {
    $options = &$config?->options;  // Error: Cannot take reference of a nullsafe chain
}
```

**Fix**: Check for null, then use the regular `->` operator, or copy the value instead of referencing it.

```php
function options(?Config $config) {
    if ($config === null) {
        return;
    }
    $options = &$config->options;  // OK
}
```

---

## Undefined Elements

### PhanUndeclaredClass

A class reference doesn't correspond to any declared class.

**Example**:
```php
$user = new UnknownClass();  // Error: class doesn't exist
```

**Fix**: Declare the class or import it.

```php
use App\Models\User;

$user = new User();  // OK: class is declared
```

### PhanUndeclaredFunction

A function call references an undefined function.

**Example**:
```php
echo my_special_function();  // Error: function not defined
```

**Fix**: Define the function or import it from a library.

```php
function my_special_function() {
    return "result";
}

echo my_special_function();  // OK
```

### PhanUndeclaredMethod

A method call references a method that doesn't exist.

**Example**:
```php
class User {
    public function getName() { return "John"; }
}

$user = new User();
echo $user->getEmail();  // Error: getEmail() doesn't exist
```

**Fix**: Implement the method or call an existing one.

```php
class User {
    public function getName() { return "John"; }
    public function getEmail() { return "john@example.com"; }
}

$user = new User();
echo $user->getEmail();  // OK
```

### PhanUndeclaredProperty

A property is accessed that isn't declared in the class.

**Example**:
```php
class User {
    public $name;
}

$user = new User();
echo $user->email;  // Error: $email not declared
```

**Fix**: Declare the property or use `@property` annotation.

```php
class User {
    public $name;
    public $email;
}

$user = new User();
echo $user->email;  // OK
```

Or for dynamic properties:

```php
/**
 * @property string $email
 */
class User {
    public $name;
}

$user = new User();
echo $user->email;  // OK: documented via @property
```

### PhanUndeclaredVariable

A variable is used that was never assigned.

**Example**:
```php
if (some_condition()) {
    $result = fetchData();
}

echo $result;  // Error: $result might not be defined
```

**Fix**: Initialize the variable before use.

```php
$result = null;  // Initialize

if (some_condition()) {
    $result = fetchData();
}

echo $result;  // OK
```

### PhanPossiblyUndeclaredVariable

A variable might not be defined in some code paths.

**Example**:
```php
if ($x > 5) {
    $message = "large";
}

echo $message;  // Warning: might not be defined if $x <= 5
```

**Fix**: Ensure the variable is initialized in all paths.

```php
$message = "small";  // Default value

if ($x > 5) {
    $message = "large";
}

echo $message;  // OK: always defined
```

### PhanUndeclaredClassMethod

A static method is called on a class that doesn't have that method.

**Example**:
```php
class Config {
    public static function get($key) {
        return $_ENV[$key] ?? null;
    }
}

echo Config::getAll();  // Error: no getAll() method
```

**Fix**: Implement the method or call an existing one.

```php
class Config {
    public static function get($key) {
        return $_ENV[$key] ?? null;
    }

    public static function getAll() {
        return $_ENV;
    }
}

echo count(Config::getAll());  // OK
```

### PhanUndeclaredClassProperty

A static property is accessed that doesn't exist.

**Example**:
```php
class Settings {
    public static $debug = false;
}

echo Settings::$timeout;  // Error: $timeout not declared
```

**Fix**: Declare the property.

```php
class Settings {
    public static $debug = false;
    public static $timeout = 30;
}

echo Settings::$timeout;  // OK
```

### PhanUndeclaredConstant

A global constant is used that doesn't exist.

**Example**:
```php
echo MY_CONSTANT;  // Error: constant not defined
```

**Fix**: Define the constant or import it.

```php
define('MY_CONSTANT', 'value');

echo MY_CONSTANT;  // OK
```

Or via class constant:

```php
class Config {
    const MY_CONSTANT = 'value';
}

echo Config::MY_CONSTANT;  // OK
```

### PhanClassContainsAbstractMethod

A class has abstract methods but isn't declared abstract.

**Example**:
```php
class Handler {
    abstract public function process($data);  // Error: class must be abstract
}
```

**Fix**: Either declare the class as abstract or implement the method.

```php
abstract class Handler {
    abstract public function process($data);  // OK: class is abstract
}

class ConcreteHandler extends Handler {
    public function process($data) {
        // Implementation
    }
}
```

The same issue is reported for a hook of an abstract property, or of a property of an interface, that a non-abstract class does not implement (PHP 8.4). The hook is named like `\Shape::$area::get`. A property without hooks, or a hooked property that uses its backing value, implements both hooks.

```php
interface Shape {
    public float $area { get; set; }
}

class Square implements Shape {
    public float $area { get => 1.0; }  // Error: Shape::$area::set is not implemented
}
```

---

## Type Mismatches

### PhanTypeMismatchArgument

A function argument has the wrong type.

**Example**:
```php
/**
 * @param int $count
 */
function setLimit($count) {
    // ...
}

setLimit("50");  // Error: string passed, int expected
```

**Fix**: Pass the correct type.

```php
setLimit(50);  // OK: int passed

// Or convert the string
setLimit((int) "50");  // OK: explicit cast
```

### PhanTypeMismatchReturn

A function returns the wrong type.

**Example**:
```php
/**
 * @return int
 */
function getCount() {
    return "0";  // Error: returns string, int expected
}
```

**Fix**: Return the correct type.

```php
/**
 * @return int
 */
function getCount() {
    return 0;  // OK: returns int
}
```

### PhanTypeMismatchProperty

A property is assigned a value of the wrong type.

**Example**:
```php
class User {
    /** @var string */
    public $name;
}

$user = new User();
$user->name = 123;  // Error: int assigned to string property
```

**Fix**: Assign the correct type.

```php
$user->name = "John";  // OK: string assigned
```

### PhanTypeMismatchDimAssignment

An array element is assigned the wrong type.

**Example**:
```php
/** @var array<int, string> */
$names = [];

$names[0] = 123;  // Error: int assigned to string array
```

**Fix**: Assign the correct type.

```php
$names[0] = "John";  // OK: string assigned
```

### PhanPossiblyNullTypeArgument

An argument might be null, but the parameter doesn't accept null.

**Example**:
```php
/**
 * @param string $name
 */
function greet($name) {
    echo "Hello, $name";
}

$user_name = getUserName();  // Returns string|null

greet($user_name);  // Error: might be null
```

**Fix**: Check for null or change the parameter type.

```php
// Option 1: Check for null
if ($user_name !== null) {
    greet($user_name);
}

// Option 2: Accept null
/**
 * @param string|null $name
 */
function greet($name) {
    echo "Hello, " . ($name ?? "Guest");
}

greet($user_name);  // OK
```

### PhanPossiblyFalseTypeArgument

An argument might be `false`, but the parameter doesn't accept it.

**Example**:
```php
/**
 * @param string $data
 */
function process($data) {
    echo strlen($data);
}

$result = file_get_contents("file.txt");  // Returns string|false

process($result);  // Error: might be false
```

**Fix**: Check for false.

```php
$result = file_get_contents("file.txt");

if ($result !== false) {
    process($result);  // OK
}
```

### PhanTypeMismatchDefault

A property has a default value of the wrong type.

**Example**:
```php
class Config {
    /** @var int */
    public $timeout = "30";  // Error: string default for int property
}
```

**Fix**: Use the correct type for the default.

```php
class Config {
    /** @var int */
    public $timeout = 30;  // OK: int default
}
```

---

## Type Inference

### PhanPossiblyNullTypeMismatchProperty

A property might be null, but code treats it as non-null.

**Example**:
```php
class User {
    /** @var string|null */
    public $email = null;
}

function sendEmail(User $user) {
    mail($user->email, "Subject", "Body");  // Error: email might be null
}
```

**Fix**: Check for null.

```php
function sendEmail(User $user) {
    if ($user->email !== null) {
        mail($user->email, "Subject", "Body");  // OK
    }
}
```

### PhanTypeInvalidDimOffset

Array offset is of wrong type.

**Example**:
```php
/** @var array<int, string> */
$items = ["a", "b"];

$value = $items["key"];  // Error: string key on int-keyed array
```

**Fix**: Use the correct key type.

```php
$value = $items[0];  // OK: int key
```

### PhanTypeMismatchForeach

Iteration over non-traversable type.

**Example**:
```php
/**
 * @param string $value
 */
function process($value) {
    foreach ($value as $char) {  // Error: can't iterate string
        echo $char;
    }
}
```

**Fix**: Use a traversable type or convert.

```php
/**
 * @param array $items
 */
function process($items) {
    foreach ($items as $item) {  // OK: iterating array
        echo $item;
    }
}
```

### PhanTypeArrayOperator

Array operator applied to non-array.

**Example**:
```php
function getData() {
    return "not an array";
}

$data = getData();
echo $data[0];  // Error: can't index string (usually)
```

**Fix**: Ensure the type is an array.

```php
function getData() {
    return ["item1", "item2"];
}

$data = getData();
echo $data[0];  // OK: string is indexable, warning still possible
```

### PhanTypeInstantiateAbstract

Attempting to instantiate an abstract class.

**Example**:
```php
abstract class Vehicle {
    abstract public function start();
}

$vehicle = new Vehicle();  // Error: can't instantiate abstract class
```

**Fix**: Use a concrete subclass.

```php
class Car extends Vehicle {
    public function start() {
        echo "Engine started";
    }
}

$vehicle = new Car();  // OK: concrete class
```

### PhanTypeInstantiateInterface

Attempting to instantiate an interface.

**Example**:
```php
interface Logger {
    public function log($message);
}

$logger = new Logger();  // Error: can't instantiate interface
```

**Fix**: Implement the interface and instantiate the concrete class.

```php
class ConsoleLogger implements Logger {
    public function log($message) {
        echo $message;
    }
}

$logger = new ConsoleLogger();  // OK
```

---

## Access Control

### PhanAccessOwnConstructor

Calling `__construct()` directly rather than using `parent::__construct()` or `new`.

**Example**:
```php
class Base {
    public function __construct($value) {
        $this->value = $value;
    }
}

class Child extends Base {
    public function __construct($value) {
        $this->__construct($value);  // Error: recursive call
    }
}
```

**Fix**: Call parent's constructor properly.

```php
class Child extends Base {
    public function __construct($value) {
        parent::__construct($value);  // OK: calls parent
    }
}
```

### PhanAccessPrivateMethod

A private method is called from outside the class.

**Example**:
```php
class User {
    private function hashPassword($pwd) {
        return md5($pwd);
    }
}

$user = new User();
$user->hashPassword("secret");  // Error: private method
```

**Fix**: Make it public/protected or call from within the class.

```php
class User {
    public function setPassword($pwd) {
        $this->password = $this->hashPassword($pwd);
    }

    private function hashPassword($pwd) {
        return md5($pwd);
    }
}

$user = new User();
$user->setPassword("secret");  // OK
```

### PhanAccessPrivateProperty

A private property is accessed from outside the class.

**Example**:
```php
class User {
    private $ssn;
}

$user = new User();
echo $user->ssn;  // Error: private property
```

**Fix**: Use a public accessor method.

```php
class User {
    private $ssn;

    public function getSsn() {
        return $this->ssn;
    }
}

$user = new User();
echo $user->getSsn();  // OK
```

### PhanAccessProtectedMethod

A protected method is called outside a related class.

**Example**:
```php
class Parent {
    protected function internalMethod() { }
}

class Unrelated {
    public function test() {
        $p = new Parent();
        $p->internalMethod();  // Error: not a subclass
    }
}
```

**Fix**: Only call protected methods from the class or subclasses.

```php
class Parent {
    protected function internalMethod() { }
}

class Child extends Parent {
    public function test() {
        $this->internalMethod();  // OK: subclass
    }
}
```

---

## Object/Class Issues

### PhanInvalidInstanceof

Using `instanceof` with non-class type.

**Example**:
```php
if ($value instanceof string) {  // Error: string is not a class
    echo $value;
}
```

**Fix**: Check with a class or use a type check function.

```php
if (is_string($value)) {  // OK: type check function
    echo $value;
}
```

### PhanNonClassMethodCall

Calling a method on something that isn't a class instance.

**Example**:
```php
$value = "string";
$value->method();  // Error: strings don't have methods
```

**Fix**: Ensure you're calling methods on objects.

```php
$user = new User();
$user->getName();  // OK: calling method on object
```

### PhanTypeInvalidThrowsNonThrowable

Throwing something that doesn't extend Throwable.

**Example**:
```php
class NotAnException {
    // ...
}

throw new NotAnException();  // Error: doesn't extend Throwable
```

**Fix**: Extend Exception or Throwable.

```php
class CustomException extends Exception {
    // ...
}

throw new CustomException();  // OK
```

### PhanInvalidMagicMethodSignature

A magic method is declared with a signature that PHP rejects when it compiles the class. PHP checks:

- the number of parameters (`__get` takes exactly 1, `__set` exactly 2, `__toString` none; a variadic parameter is not counted),
- that no parameter is passed by reference,
- `static`: `__callStatic` and `__set_state` must be static, the others cannot be,
- the declared type of a parameter, which must allow the `string` or `array` that PHP passes,
- the declared return type: `void` for `__clone`, `__set`, `__unset`, `__unserialize` and `__wakeup`, `bool` for `__isset`, `string` for `__toString`, `array` for `__serialize` and `__sleep`, `?array` for `__debugInfo`, `object` for `__set_state`. `never` is always allowed. `__construct` and `__destruct` cannot declare a return type,
- `#[\NoDiscard]` on a magic method that returns nothing (PHP 8.5+).

A magic method without declared types only has to have the right number of parameters.

**Example**:
```php
class Config {
    public function __get() {}                       // Error: it must take exactly 1 argument
    public function __set($name, &$value) {}         // Error: it cannot take arguments by reference
    public function __callStatic($name, $args) {}    // Error: it must be static
    public function __isset(int $name): bool {}      // Error: parameter #1 ($name) must be of type string when declared
    public function __toString(): ?string {}         // Error: the return type must be string when declared
}
```

**Fix**: Use the signature that PHP expects.

```php
class Config {
    public function __get(string $name): mixed { return null; }
    public function __set(string $name, mixed $value): void {}
    public static function __callStatic(string $name, array $args): mixed { return null; }
    public function __isset(string $name): bool { return false; }
    public function __toString(): string { return ''; }
}
```

### PhanMagicMethodNonPublic

A magic method other than `__construct`, `__destruct` and `__clone` is not public. PHP emits a warning when it compiles the class (`The magic method C::__get() must have public visibility`).

**Example**:
```php
class Config {
    private function __get($name) { return null; }  // Warning: must have public visibility
}
```

**Fix**: Declare the method as `public`.

### PhanDeprecatedBacktickOperator

The backtick operator is used. It is deprecated in PHP 8.5. Emitted when `target_php_version` is 8.5 or newer.

**Example**:
```php
echo `ls -l`;  // Deprecated in PHP 8.5
```

**Fix**: Call `shell_exec()`.

```php
echo shell_exec('ls -l');
```

### PhanDeprecatedStringIncrement

`++` or `--` is applied to a value that is known to be a non-numeric string.

- `--` on a non-numeric string has no effect, and is deprecated in PHP 8.3.
- `++` on an empty string, or on a string that is not alphanumeric, is deprecated in PHP 8.3.
- `++` on any other non-numeric string (`'a'` becomes `'b'`, `'Az'` becomes `'Ba'`) is deprecated in PHP 8.5.

Emitted when `target_php_version` is at least the version of the deprecation. Numeric strings such as `'5'` are not affected.

**Example**:
```php
$column = 'A';
$column++;  // Deprecated in PHP 8.5
```

**Fix**: Use `str_increment()` or `str_decrement()` (PHP 8.3+).

```php
$column = str_increment('A');
```

### PhanDeprecatedNullArrayOffset

`null` is used as the offset of an array. PHP converts it to an empty string; this is deprecated in PHP 8.5. Emitted when `target_php_version` is 8.5 or newer, for reads, writes, `isset()` and array literals. It is not reported for an `ArrayAccess` object, or when Phan is not certain that the value is an array.

**Example**:
```php
function first(array $values) {
    return $values[null];  // Deprecated in PHP 8.5
}
$map = [null => 1];        // Deprecated in PHP 8.5
```

**Fix**: Use an empty string.

```php
function first(array $values) {
    return $values[''];
}
```

### PhanDeprecatedReservedName

A name is used that PHP deprecates in order to reserve it for future syntax. Emitted when `target_php_version` is at least the version of the deprecation.

- `_` as the name of a class, interface, trait or enum (PHP 8.4).
- `let` and `is` as the name of a class, interface, trait, enum or function, and `readonly` as the name of a function (PHP 8.6).
- `namespace` as the name of a class constant or an enum case (PHP 8.6).
- `let`, `is` and `_` as the name of a global constant (PHP 8.6).
- `let`, `is` and `_` as the alias of a `use` statement for a class or constant, and `let` and `is` for a function, including `use Foo\let;` (PHP 8.6).

**Example**:
```php
use Vendor\Lexer as let;  // Deprecated in PHP 8.6
class _ {}                // Deprecated in PHP 8.4
```

**Fix**: Choose another name or alias.

### PhanReservedClassName

A name that PHP reserves for its types is used where PHP stops with a fatal error. The reserved names are `bool`, `false`, `float`, `int`, `null`, `parent`, `self`, `static`, `string`, `true`, `void`, `never`, `iterable`, `object` and `mixed` (and `array` and `callable` for aliases), in any case.

- The name of a class, interface, trait or enum, in any namespace: `class int {}`.
- The alias of a `use` statement for a class, including `use Foo\string;`. Aliases of `use function` and `use const` are not affected.
- `self`, `parent` or `static` after `extends` or `implements`.
- A qualified type: `\self`, `\parent`, `\static`, `Foo\int`, also as a member of a union or intersection type.

**Example**:
```php
namespace App;

use Vendor\Text as string;      // Cannot use "string" as the alias of \Vendor\Text as it is reserved
class Mixed {}                  // Cannot use "Mixed" as a class name as it is reserved
class Child extends parent {}   // Cannot use "parent" as a class name as it is reserved
function f(\self $x) {}         // Cannot use "\self" as a qualified class name as it is reserved
```

**Fix**: Choose another name or alias, and write `self`, `parent` and `static` without a namespace separator.

### PhanDeprecatedNullableDebugInfoReturnType

`__debugInfo()` is declared with a nullable return type. Returning null from `__debugInfo()` is deprecated in PHP 8.6. Emitted when `target_php_version` is 8.6 or newer.

**Example**:
```php
class Config {
    public function __debugInfo(): ?array { return null; }  // Deprecated in PHP 8.6
}
```

**Fix**: Make the return type non-nullable and return an empty array.

```php
class Config {
    public function __debugInfo(): array { return []; }
}
```

### PhanReadonlyPropertyHasSetHook

A readonly property has a set hook.

**Example**:
```php
class User {
    public readonly string $id {
        set { $this->id = $value; }  // Error: readonly can't have set hook
    }
}
```

**Fix**: Remove the `readonly` keyword if you want a set hook.

```php
class User {
    public string $id {
        get { return $this->id; }
        set { $this->id = $value; }  // OK: not readonly
    }
}
```

### PhanAccessOverridesFinalProperty

A class declares a property that is final in an ancestor class (PHP 8.4). This includes final promoted constructor properties (PHP 8.5). A property that is `private(set)` is implicitly final. PHP rejects the declaration (`Cannot override final property`).

**Example**:
```php
class Base {
    final public int $id = 0;
}

class Child extends Base {
    public int $id = 1;  // Error: Base::$id is final
}
```

**Fix**: Remove the redeclaration, or remove `final` from the property of the ancestor.

### PhanPropertyHookFinalOverride

A class declares a hook that is final in the same property of an ancestor class (PHP 8.4). The other hook of the property can still be declared.

**Example**:
```php
class Base {
    public int $id {
        final get => 1;
    }
}

class Child extends Base {
    public int $id {
        get => 2;                  // Error: Base::$id::get is final
        set { $this->id = $value; }  // OK
    }
}
```

**Fix**: Remove the hook, or remove `final` from the hook of the ancestor.

### PhanTypeUnsetStaticProperty

Attempting to `unset()` a static property. PHP always throws `Error: Attempt to unset static property` at runtime, whatever the class or visibility, and `php -l` does not catch it.

**Example**:
```php
class Cache {
    private static $entries = [];

    public static function clear() {
        unset(self::$entries);  // Error: Attempt to unset static property Cache::$entries
    }
}
```

**Fix**: Assign a new value instead. Unsetting an element of the property's value is allowed.

```php
class Cache {
    private static $entries = [];

    public static function clear() {
        self::$entries = [];        // OK
    }

    public static function remove(string $key) {
        unset(self::$entries[$key]);  // OK: modifies the array, not the property
    }
}
```

---

## Deprecated

### PhanDeprecatedFunction

A deprecated function is called.

**Example**:
```php
/**
 * @deprecated Use newFunction() instead
 */
function oldFunction() {
    // ...
}

oldFunction();  // Warning: function is deprecated
```

**Fix**: Use the recommended replacement.

```php
newFunction();  // OK: use current function
```

### PhanDeprecatedMethod

A deprecated method is called.

**Example**:
```php
class User {
    /**
     * @deprecated Use getFullName() instead
     */
    public function getName() {
        return $this->name;
    }

    public function getFullName() {
        return $this->firstName . " " . $this->lastName;
    }
}

$user = new User();
echo $user->getName();  // Warning: method is deprecated
```

**Fix**: Call the replacement method.

```php
echo $user->getFullName();  // OK
```

### PhanDeprecatedClassConstant

A deprecated class constant is referenced.

**Example**:
```php
class Config {
    /**
     * @deprecated Use PHP_VERSION_ID instead
     */
    const PHP_VERSION = "8.0";
}

echo Config::PHP_VERSION;  // Warning: constant is deprecated
```

**Fix**: Use the recommended constant.

```php
echo PHP_VERSION_ID;  // OK
```

---

## Generic/Template Issues

### PhanTemplateTypeConstrained

A template type violates its constraints.

**Example**:
```php
/**
 * @template T of string|int
 * @param T $value
 */
function process($value) {
    // ...
}

process([]);  // Error: array doesn't satisfy T constraint
```

**Fix**: Pass a value that satisfies the constraint.

```php
process("value");  // OK: string satisfies constraint
process(42);       // OK: int satisfies constraint
```

### PhanTemplateTypeViolation

A return type violates template type constraints.

**Example**:
```php
/**
 * @template T
 * @param T $value
 * @return T
 */
function identity($value) {
    return 42;  // Error: return type doesn't match T
}
```

**Fix**: Return the correct type.

```php
/**
 * @template T
 * @param T $value
 * @return T
 */
function identity($value) {
    return $value;  // OK: returns T
}
```

### PhanCommentReturnConditionalWithoutRealParam

A [conditional return type](Annotating-Your-Source-Code-V6#conditional-return-types-v6) tests a parameter that the function doesn't declare.

**Example**:
```php
/**
 * @return ($nope is null ? int : string)
 */
function convert(int $x) {  // Error: $nope is not a parameter of convert()
    // ...
}
```

**Fix**: Reference an actual parameter name.

```php
/**
 * @return ($x is 0 ? int : string)
 */
function convert(int $x) {
    // ...
}
```

---

## Plugin-Specific

### PhanUnreferencedPublicMethod

A public method is never called.

**Example**:
```php
class Helper {
    public function unusedMethod() {  // Warning: never called
        return "result";
    }
}
```

**Fix**: Either remove the method or call it somewhere.

```php
class Helper {
    public function usedMethod() {
        return "result";
    }
}

echo Helper::usedMethod();  // OK: method is used
```

### PhanUnreferencedPrivateTraitMethod

Emitted by `--dead-code-detection` for a private method declared in a trait that has no references, instead of `PhanUnreferencedPrivateMethod`. The same split exists for the other private trait elements: `PhanUnreferencedPrivateTraitProperty`, `PhanUnreferencedPrivateTraitClassConstant`, `PhanReadOnlyPrivateTraitProperty` and `PhanWriteOnlyPrivateTraitProperty`.

A private element of a trait is not really private to the trait: every class using the trait gets a copy it can use. A library that ships a trait therefore does not necessarily contain all of its users, so these issue types can be suppressed separately from the ones for private elements of classes. A suppression of the issue type they were split from (for example `@suppress PhanUnreferencedPrivateMethod` on a trait method, or `PhanUnreferencedPrivateProperty` in `suppress_issue_types`) also suppresses the trait variant.

**Example**:
```php
trait Loggable {
    private function formatMessage(string $message): string {  // Warning: never called in the analyzed code
        return '[' . static::class . '] ' . $message;
    }
}
```

**Fix**: Call it, remove it, or, for a library whose users live outside the analyzed code, suppress the trait variants in the config.

```php
'suppress_issue_types' => [
    'PhanUnreferencedPrivateTraitMethod',
    'PhanUnreferencedPrivateTraitProperty',
    'PhanUnreferencedPrivateTraitClassConstant',
    'PhanReadOnlyPrivateTraitProperty',
    'PhanWriteOnlyPrivateTraitProperty',
],
```

### PhanUnusedVariable

A variable is assigned but never used.

**Example**:
```php
$value = getData();  // Warning: value never used
echo "done";
```

**Fix**: Use the variable or remove the assignment.

```php
$value = getData();
echo $value;  // OK: variable is used
```

---

## Suppressing Issues

### Method 1: Inline Suppression

Suppress a specific issue in code:

```php
/**
 * @suppress PhanTypeMismatchArgument
 */
function legacyFunction() {
    myFunction("string");  // This error is suppressed
}
```

### Method 2: File-level Suppression

Suppress multiple issues in a file:

```php
<?php
/**
 * @phan-suppress-all PhanUndeclaredFunction, PhanUndeclaredClass
 */

myFunction();  // Suppressed
new UnknownClass();  // Suppressed
```

### Method 3: Configuration

Configure issue handling in `config.php`:

```php
return [
    'suppress_issue_types' => [
        'PhanUnreferencedPublicMethod',
        'PhanUnusedVariable',
    ],
];
```

### Method 4: Baseline Files

Track acceptable issues over time:

```bash
# Create baseline
phan --save-baseline .phan/.baseline.php

# Run against baseline (only NEW issues reported)
phan --load-baseline .phan/.baseline.php
```

---

## Finding Issue Details

To get comprehensive details on any issue:

1. **In Phan output**: The error message includes the issue type in square brackets
   ```
   file.php:10 [PhanTypeMismatchArgument] Argument 1
   ```

2. **Search the codebase**: Look at `src/Phan/Issue.php` for all issue type names

3. **Check the NEWS**: See recent issues added in `NEWS.md`

4. **Use help**:
   ```bash
   phan --help | grep -i issue
   ```

---

## See Also

- [[Annotating-Your-Source-Code-V6]] - How to add type hints to avoid issues
- [[Generic-Types-V6]] - Using templates and generics
- [[Using-Phan-From-Command-Line]] - CLI options for configuring issue reporting
- [[Home]] - Main documentation index

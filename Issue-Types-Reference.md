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

A property with hooks is declared with a default value.

**Example** (PHP 8.4):
```php
public string $name = "default" {  // Error: hooked properties can't have defaults
    get { return $this->name; }
}
```

**Fix**: Remove the default value from hooked properties.

```php
public string $name {
    get { return $this->name; }
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

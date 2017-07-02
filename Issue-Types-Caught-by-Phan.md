See [\Phan\Issue](https://github.com/etsy/phan/blob/master/src/Phan/Issue.php) for the set of error types that are emitted. Below is a listing of all issue types as of [8c1435](https://github.com/etsy/phan/tree/8c1435f6044f15fa4fd39c2abf713062214f4087/). The test case [0101_one_of_each.php](https://github.com/etsy/phan/blob/master/tests/files/src/0101_one_of_each.php) should cover all examples in this document.

Please add example code, fix outdated info and add any remedies to the issues below.

[![Gitter](https://badges.gitter.im/etsy/phan.svg)](https://gitter.im/etsy/phan?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

# AccessError

This category of issue is emitted when you're trying to access things that you can't access.

## PhanAccessClassConstantInternal

This issue comes up when there is an attempt to access a `@internal` class constant outside of the namespace in which it's defined.

```
Cannot access internal class constant {CONST} defined at {FILE}:{LINE}
```

## PhanAccessPropertyPrivate

This issue comes up when there is an attempt to access a private property outside of the scope in which it's defined.

```
Cannot access private property {PROPERTY}
```

This will be emitted for the following code.

```php
class C1 { private static $p = 42; }
print C1::$p;
```

## PhanAccessPropertyProtected

This issue comes up when there is an attempt to access a protected property outside of the scope in which it's defined or an implementing child class.

```
Cannot access protected property {PROPERTY}
```

This will be emitted for the following code.

```php
class C1 { protected static $p = 42; }
print C1::$p;
```

# CompatError

This category of issue is emitted when there are compatibility issues. They will be thrown if there is an expression that may be treated differently in PHP7 than it was in previous major versions of the PHP runtime. Take a look at the [PHP7 Migration Manual](http://php.net/manual/en/migration70.incompatible.php) to understand changes in behavior.

## PhanCompatibleExpressionPHP7

This issue will be thrown if there is an expression that may be treated differently in PHP7 than it was in previous major versions of the PHP runtime. Take a look at the [PHP7 Migration Manual](http://php.net/manual/en/migration70.incompatible.php) to understand changes in behavior.

The config `Config::get()->backward_compatibility_checks` must be enabled for this to run such as by passing the command line argument `--backward-compatibility-checks` or by defining it in a `.phan/config.php` file such as [Phan's own config](https://github.com/etsy/phan/blob/master/.phan/config.php).

```
{CLASS} expression may not be PHP 7 compatible
```

## PhanCompatiblePHP7

This issue will be thrown if there is an expression that may be treated differently in PHP7 than it was in previous major versions of the PHP runtime. Take a look at the [PHP7 Migration Manual](http://php.net/manual/en/migration70.incompatible.php) to understand changes in behavior.

The config `Config::get()->backward_compatibility_checks` must be enabled for this to run such as by passing the command line argument `--backward-compatibility-checks` or by defining it in a `.phan/config.php` file such as [Phan's own config](https://github.com/etsy/phan/blob/master/.phan/config.php).

```
Expression may not be PHP 7 compatible
```

This will be emitted for the following code.

```php
$c->$m[0]();
```

# Context

This category of issue are for when you're doing stuff out of the context in which you're allowed to do it like referencing `self` or `parent` when not in a class, interface or trait.

## PhanContextNotObject

This issue comes up when you attempt to use things like `$this` that only exist when you're inside of a class, trait or interface, but are not.

```
Cannot access {CLASS} when not in object context
```

This will be emitted for the following code.

```php
new parent;
```

# DeprecatedError

This category of issue comes up when you're accessing deprecated elements (as marked by the `@deprecated` comment).

**Note!** Only classes, traits, interfaces, methods, functions, properties, and traits may be marked as deprecated. You can't deprecate a variable or any other expression.

## PhanDeprecatedFunction

If a class, method, function, property or constant is marked in its comment as `@deprecated`, any references to them will emit a deprecated error.

```
Call to deprecated function {FUNCTIONLIKE}() defined at {FILE}:{LINE}
```

This will be emitted for the following code.

```php
/** @deprecated  */
function f1() {}
f1();
```

# NOOPError

This category of issues are emitted when you have reasonable code but it isn't doing anything. They're all low severity.

## PhanNoopArray

Emitted when you have an array that is not used in any way.

```
Unused array
```

This will be emitted for the following code.

```php
[1,2,3];
```

## PhanNoopClosure

Emitted when you have a closure that is unused.

```
Unused closure
```

This will be emitted for the following code.

```php
function () {};
```

## PhanNoopConstant

Emitted when you have a reference to a constant that is unused.

```
Unused constant
```

This will be emitted for the following code.

```php
const C = 42;
C;
```

## PhanNoopProperty

Emitted when you have a refence to a property that is unused.

```
Unused property
```

This will be emitted for the following code.

```php
class C {
    public $p;
    function f() {
        $this->p;
    }
}
```

## PhanNoopVariable

Emitted when you have a reference to a variable that is unused.

```
Unused variable
```

This will be emitted for the following code.

```php
$a = 42;
$a;
```

## PhanUnreferencedClass

Similar issues exist for PhanUnreferencedProperty, PhanUnreferencedConstant, and PhanUnreferencedFunction

This issue is disabled by default, but can be enabled by setting `Config::get()->dead_code_detection` to enabled. It indicates that the given element is (possibly) unused.

```
Possibly zero references to class {CLASS}
```

This will be emitted for the following code so long as `Config::get()->dead_code_detection` is enabled.

```php
class C {}
```

Keep in mind that for the following code we'd still emit the issue.

```php
class C2 {}
$v = 'C2';
new $v;

$v2 = 'C' . (1 + 1);
new $v2;
```

YMMV.

# ParamError

This category of error comes up when you're messing up your method or function parameters in some way.

## PhanParamReqAfterOpt

If you declare a function with required parameters after optional parameters, you'll see this issue.

```
Required argument follows optional
```

This will be emitted for the following code

```php
function f2($p1 = null, $p2) {}
```

## PhanParamSpecial1

```
Argument {INDEX} ({PARAMETER}) is {TYPE} but {FUNCTIONLIKE}() takes {TYPE} when argument {INDEX} is {TYPE}
```

## PhanParamSpecial2

```
Argument {INDEX} ({PARAMETER}) is {TYPE} but {FUNCTIONLIKE}() takes {TYPE} when passed only one argument
```

## PhanParamSpecial3

```
The last argument to {FUNCTIONLIKE} must be of type {TYPE}
```

## PhanParamSpecial4

```
The second to last argument to {FUNCTIONLIKE} must be of type {TYPE}
```

## PhanParamTooFew

This issue indicates that you're not passing in at least the number of required parameters to a function or method.

```
Call with {COUNT} arg(s) to {FUNCTIONLIKE}() which requires {COUNT} arg(s) defined at {FILE}:{LINE}
```

This will be emitted for the code

```php
function f6($i) {}
f6();
```

## PhanParamTooFewInternal

This issue indicates that you're not passing in at least the number of required parameters to an internal function or method.

```
Call with {COUNT} arg(s) to {FUNCTIONLIKE}() which requires {COUNT} arg(s)
```

This will be emitted for the code

```php
strlen();
```

## PhanParamTooMany

This issue is emitted when you're passing more than the number of required and optional parameters than are defined for a method or function.

```
Call with {COUNT} arg(s) to {FUNCTIONLIKE}() which only takes {COUNT} arg(s) defined at {FILE}:{LINE}
```

This will be emitted for the code

```php
function f7($i) {}
f7(1, 2);
```

## PhanParamTooManyInternal

This issue is emitted when you're passing more than the number of required and optional parameters than are defined for an internal method or function.

```
Call with {COUNT} arg(s) to {FUNCTIONLIKE}() which only takes {COUNT} arg(s)
```

This will be emitted for the code

```php
strlen('str', 42);
```

## PhanParamTypeMismatch

```
Argument {INDEX} is {TYPE} but {FUNCTIONLIKE}() takes {TYPE}
```

# RedefineError

This category of issue come up when more than one thing of whatever type have the same name and namespace.

## PhanRedefineClass

If you attempt to create a class that has the same name and namespace as a class that exists elsewhere you'll see this issue. Note that you'll get the issue on the second class in the order of files passed in to Phan.

```
{CLASS} defined at {FILE}:{LINE} was previously defined as {CLASS} at {FILE}:{LINE}
```

This issue will be emitted for code like

```php
class C15 {}
class C15 {}
```

## PhanRedefineClassInternal

If you attempt to create a class that has the same name and namespace as a class that is internal to PHP, you'll see this issue.

```
{CLASS} defined at {FILE}:{LINE} was previously defined as {CLASS} internally
```

This issue will be emitted for code like

```php
class DateTime {}
```

## PhanRedefineFunction

This issue comes up when you have two functions (or methods) with the same name and namespace.

```
Function {FUNCTION} defined at {FILE}:{LINE} was previously defined at {FILE}:{LINE}
```

It'll come up with code like

```php
function f9() {}
function f9() {}
```

## PhanRedefineFunctionInternal

This issue comes up if you define a function or method that has the same name and namespace as an internal function or method.

```
Function {FUNCTION} defined at {FILE}:{LINE} was previously defined internally
```

You'll see this issue with code like

```php
function strlen() {}
```

# StaticCallError

## PhanStaticCallToNonStatic

If you make a static call to a non static method, you'll see this issue.

```
Static call to non-static method {METHOD} defined at {FILE}:{LINE}
```

An example of this issue would come from the following code.

```php
class C19 { function f() {} }
C19::f();
```

# TypeError

This category of issue come from using incorrect types or types that cannot cast to the expected types.

## PhanNonClassMethodCall

If you call a method on a non-class element, you'll see this issue.

```
Call to method {METHOD} on non-class type {TYPE}
```

An example would come from

```php
$v8 = null;
$v8->f();
```

## PhanTypeArrayOperator

```
Invalid array operator
```

## PhanTypeArraySuspicious

Attempting to treat a non-array or non-string element as an array will get you this issue.

```
Suspicious array access to {TYPE}
```

This issue will be emitted for the following code

```php
$a = false; if($a[1]) {}
```

## PhanTypeComparisonFromArray

Comparing an array to a non-array will result in this issue.

```
array to {TYPE} comparison
```

An example would be

```php
if ([1, 2] == 'string') {}
```

## PhanTypeComparisonToArray

Comparing a non-array to an array will result in this issue.

```
{TYPE} to array comparison
```

An example would be

```php
if (42 == [1, 2]) {}
```

## PhanTypeConversionFromArray

```
array to {TYPE} conversion
```

## PhanTypeInstantiateAbstract

```
Instantiation of abstract class {CLASS}
```

This issue will be emitted for the following code

```php
abstract class D {} (new D);
```

## PhanTypeInstantiateInterface

```
Instantiation of interface {INTERFACE}
```

This issue will be emitted for the following code

```php
interface E {} (new E);
```

## PhanTypeInvalidLeftOperand

```
Invalid operator: right operand is array and left is not
```

## PhanTypeInvalidRightOperand

```
Invalid operator: left operand is array and right is not
```

## PhanTypeMismatchArgument

```
Argument {INDEX} ({VARIABLE}) is {TYPE} but {FUNCTIONLIKE}() takes {TYPE} defined at {FILE}:{LINE}
```

This will be emitted for the code

```php
function f8(int $i) {}
f8('string');
```

## PhanTypeMismatchArgumentInternal

```
Argument {INDEX} ({VARIABLE}) is {TYPE} but {FUNCTIONLIKE}() takes {TYPE}
```

This will be emitted for the code

```php
strlen(42);
```

## PhanTypeMismatchDefault

```
Default value for {TYPE} ${VARIABLE} can't be {TYPE}
```

## PhanTypeMismatchForeach

This issue will be emitted when something that can't be an array is passed as the array_expression.

```
{TYPE} passed to foreach instead of array
```

This will be emitted for the code

```php
foreach (null as $i) {}
```

## PhanTypeMismatchProperty

```
Assigning {TYPE} to property but {PROPERTY} is {TYPE}
```

This issue is emitted from the following code

```php
function f(int $p = false) {}
```

## PhanTypeMismatchReturn

```
Returning type {TYPE} but {FUNCTIONLIKE}() is declared to return {TYPE}
```

This issue is emitted from the following code

```php
class G { function f() : int { return 'string'; } }
```

## PhanTypeMissingReturn

```
Method {METHOD} is declared to return {TYPE} but has no return value
```

This issue is emitted from the following code

```php
class H { function f() : int {} }
```

## PhanTypeNonVarPassByRef

```
Only variables can be passed by reference at argument {INDEX} of {FUNCTIONLIKE}()
```

This issue is emitted from the following code

```php
class F { static function f(&$v) {} } F::f('string');
```

## PhanTypeParentConstructorCalled

```
Must call parent::__construct() from {CLASS} which extends {CLASS}
```

## PhanUndeclaredTypeParameter

If you have a parameter on a function or method of a type that is not defined, you'll see this issue.

```
Parameter of undeclared type {TYPE}
```

This issue will be emitted from the following code

```php
function f(Undef $p) {}
```

## PhanUndeclaredTypeProperty

If you have a property with an undefined type, you'll see this issue.

```
Property {PROPERTY} has undeclared type {TYPE}
```

This issue will be emitted from the following code

```php
class D { /** @var Undef */ public $p; }
```

## PhanTypeVoidAssignment

This is triggered by assigning the return value of a function or method that returns void.

```
Cannot assign void return value
```

This issue will be emitted from the following code:

```php
class A { /** @return void */ function v() {} }
$a = (new A)->v();
```

# UndefError

This category of issue come up when there are references to undefined things. These are a big source of false-positives in Phan given that code bases often take liberties with calling methods on sub-classes of the class defined to be returned by a function and things like that.

You can ignore all errors of this category by passing in the command-line argument `-i` or `--ignore-undeclared`.

## PhanEmptyFile

This low severity issue is emitted for empty files.

```
Empty file {FILE}
```

This would be emitted if you have a file with the contents

```php
```

## PhanParentlessClass

If there is a reference to the parent of a class that does not extend something, you'll see this issue.

```
Reference to parent of class {CLASS} that does not extend anything
```

This issue will be emitted from the following code

```php
class F { function f() { $v = parent::f(); } }
```

## PhanTraitParentReference

If you reference `parent` from within a trait, you'll get this issue. This is a low priority issue given that it is legal in PHP, but for general-purpose traits you should probably avoid this pattern.

```
Reference to parent from trait {TRAIT}
```

This issue will be emitted from the following code

```php
trait T { function f() { return parent::f(); } }
```

## PhanUnanalyzable

This issue will be emitted when we hit a structure that Phan doesn't know how to parse. More commonly this will be expressed by Phan having an uncaught exception or behaving poorly.

```
Expression is unanalyzable or feature is unimplemented. Please create an issue at https://github.com/etsy/phan/issues/new.
```

Please do file an issue or otherwise get in touch if you get one of these (or an uncaught exception, or anything else thats shitty).

[![Gitter](https://badges.gitter.im/etsy/phan.svg)](https://gitter.im/etsy/phan?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)


## PhanUndeclaredClass

```
Reference to undeclared class {CLASS}
```

## PhanUndeclaredClassCatch

If you're catching a throwable of a type that isn't defined, you'll see this issue.

```
Catching undeclared class {CLASS}
```

The following code will emit this error.

```php
try {} catch (Undef $exception) {}
```

## PhanUndeclaredClassConstant

```
Reference to constant {CONST} from undeclared class {CLASS}
```

## PhanUndeclaredClassInstanceof

```
Checking instanceof against undeclared class {CLASS}
```

This issue will be emitted from the following code

```php
$v = null;
if ($v instanceof Undef) {}
```

## PhanUndeclaredClassMethod

```
Call to method {METHOD} from undeclared class {CLASS}
```

This issue will be emitted from the following code

```php
function g(Undef $v) { $v->f(); }
```

## PhanUndeclaredClassReference

```
Reference to undeclared class {CLASS}
```

## PhanUndeclaredConstant

This issue comes up when you reference a constant that doesn't exist.

```
Reference to undeclared constant {CONST}
```

You'll see this issue with code like

```php
$v7 = UNDECLARED_CONSTANT;
```
## PhanUndeclaredExtendedClass

You'll see this issue if you extend a class that doesn't exist.

```
Class extends undeclared class {CLASS}
```

This issue will be emitted from the following code

```php
class E extends Undef {}
```

## PhanUndeclaredFunction

This issue will be emitted if you reference a function that doesn't exist.

```
Call to undeclared function {FUNCTION}
```

This issue will be emitted for the code

```php
f10();
```

## PhanUndeclaredInterface

Implementing an interface that doesn't exist or otherwise can't be found will emit this issue.

```
Class implements undeclared interface {INTERFACE}
```

The following code will express this issue.

```php
class C17 implements C18 {}
```

## PhanUndeclaredMethod

```
Call to undeclared method {METHOD}
```

## PhanUndeclaredProperty

```
Reference to undeclared property {PROPERTY}
```

This issue will be emitted from the following code

```php
class C {}
$v = (new C)->undef;
```

## PhanUndeclaredStaticMethod

```
Static call to undeclared method {METHOD}
```

This issue will be emitted from the following code

```php
C::staticMethod();
```

## PhanUndeclaredStaticProperty

Attempting to read a property that doesn't exist will result in this issue. You'll also see this issue if you write to an undeclared static property so long as `Config::get()->allow_missing_property` is false (which defaults to true).

```
Static property '{PROPERTY}' on {CLASS} is undeclared
```

An example would be

```php
class C22 {}
$v11 = C22::$p;
```

## PhanUndeclaredTrait

If you attempt to use a trait that doesn't exist, you'll see this issue.

```
Class uses undeclared trait {TRAIT}
```

An example would be

```php
class C20 { use T2; }
```

## PhanUndeclaredVariable

Trying to use a variable that hasn't been defined anywhere in scope will produce this issue.

```
Variable ${VARIABLE} is undeclared
```

An example would be

```php
$v9 = $v10;
```

# VarError

## PhanVariableUseClause

```
Non-variables not allowed within use clause
```
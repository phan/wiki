See [\Phan\Issue](https://github.com/etsy/phan/blob/master/src/Phan/Issue.php) for the set of error types that are emitted. Below is a listing of all issue types as of [87ab20](https://github.com/etsy/phan/tree/87ab2044f78c0263018428ee260b1b5be2646ca6/).

Please add example code, fix outdated info and add any remedies to the issues below.


# AccessError

This category of issue is emitted when you're trying to access things that you can't access.

## PhanAccessPropertyPrivate

This issue comes up when there is an attempt to access a private property outside of the scope in which its defined.

```
Cannot access private property %s
```

This will be emitted for the following code.

```php
class C1 { private static $p = 42; }
print C1::$p;
```

## PhanAccessPropertyProtected

This issue comes up when there is an attempt to access a protected property outside of the scope in which its defined or an implementing child class.

```
Cannot access protected property %s
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
%s expression may not be PHP 7 compatible
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
Cannot access %s when not in object context
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
Call to deprecated function %s() defined at %s:%d
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
function f() {};
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

## PhanNoopZeroReferences

This issue is disabled by default, but can be enabled by setting `Config::get()->dead_code_detection` to enabled. It indicates that the given element is (possibly) unused.

```
Possibly zero references to %s
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
Argument %d (%s) is %s but %s() takes %s when argument %d is %s
```

## PhanParamSpecial2

```
Argument %d (%s) is %s but %s() takes %s when passed only one argument
```

## PhanParamSpecial3

```
The last argument to %s must be of type %s
```

## PhanParamSpecial4

```
The second to last argument to %s must be of type %s
```

## PhanParamTooFew

This issue indicates that you're not passing in at least the number of required parameters to a function or method.

```
Call with %d arg(s) to %s() which requires %d arg(s) defined at %s:%d
```

This will be emitted for the code

```php
function f6($i) {}
f6();
```

## PhanParamTooFewInternal

This issue indicates that you're not passing in at least the number of required parameters to an internal function or method.

```
Call with %d arg(s) to %s() which requires %d arg(s)
```

This will be emitted for the code

```php
strlen();
```

## PhanParamTooMany

This issue is emitted when you're passing more than the number of required and optional parameters than are defined for a method or function.

```
Call with %d arg(s) to %s() which only takes %d arg(s) defined at %s:%d
```

This will be emitted for the code

```php
function f7($i) {}
f7(1, 2);
```

## PhanParamTooManyInternal

This issue is emitted when you're passing more than the number of required and optional parameters than are defined for an internal method or function.

```
Call with %d arg(s) to %s() which only takes %d arg(s)
```

This will be emitted for the code

```php
strlen('str', 42);
```

## PhanParamTypeMismatch

```
Argument %d is %s but %s() takes %s
```

# RedefineError

This category of issue come up when more than one thing of whatever type have the same name and namespace.

## PhanRedefineClass

If you attempt to create a class that has the same name and namespace as a class that exists elsewhere you'll see this issue. Note that you'll get the issue on the second class in the order of files passed in to Phan.

```
%s defined at %s:%d was previously defined as %s at %s:%d
```

This issue will be emitted for code like

```php
class C15 {}
class C15 {}
```

## PhanRedefineClassInternal

If you attempt to create a class that has the same name and namespace as a class that is internal to PHP, you'll see this issue.

```
%s defined at %s:%d was previously defined as %s internally
```

This issue will be emitted for code like

```php
class DateTime {}
```

## PhanRedefineFunction

This issue comes up when you have two functions (or methods) with the same name and namespace.

```
Function %s defined at %s:%d was previously defined at %s:%d
```

It'll come up with code like

```php
function f9() {}
function f9() {}
```

## PhanRedefineFunctionInternal

This issue comes up if you define a function or method that has the same name and namespace as an internal function or method.

```
Function %s defined at %s:%d was previously defined internally
```

You'll see this issue with code like

```php
function strlen() {}
```

# StaticCallError

## PhanStaticCallToNonStatic

```
Static call to non-static method %s defined at %s:%d
```

# TypeError

## PhanNonClassMethodCall

```
Call to method on non-class type %s
```

## PhanTypeArrayOperator

```
Invalid array operator
```

## PhanTypeArraySuspicious

```
Suspicious array access to %s
```

This issue will be emitted for the following code

```php
$a = false; if($a[1]) {}
```

## PhanTypeComparisonFromArray

```
array to %s comparison
```

## PhanTypeComparisonToArray

```
%s to array comparison
```

## PhanTypeConversionFromArray

```
array to %s conversion
```

## PhanTypeInstantiateAbstract

```
Instantiation of abstract class %s
```

This issue will be emitted for the following code

```php
abstract class D {} (new D);
```

## PhanTypeInstantiateInterface

```
Instantiation of interface %s
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
Argument %d (%s) is %s but %s() takes %s defined at %s:%d
```

This will be emitted for the code

```php
function f8(int $i) {}
f8('string');
```

## PhanTypeMismatchArgumentInternal

```
Argument %d (%s) is %s but %s() takes %s
```

This will be emitted for the code

```php
strlen(42);
```

## PhanTypeMismatchDefault

```
Default value for %s $%s can't be %s
```

## PhanTypeMismatchForeach

This issue will be emitted when something that can't be an array is passed as the array_expression.

```
%s passed to foreach instead of array
```

This will be emitted for the code

```php
foreach (null as $i) {}
```

## PhanTypeMismatchProperty

```
Assigning %s to property but %s is %s
```

This issue is emitted from the following code

```php
function f(int $p = false) {}
```

## PhanTypeMismatchReturn

```
Returning type %s but %s() is declared to return %s
```

This issue is emitted from the following code

```php
class G { function f() : int { return 'string'; } }
```

## PhanTypeMissingReturn

```
Method %s is declared to return %s but has no return value
```

This issue is emitted from the following code

```php
class H { function f() : int {} }
```

## PhanTypeNonVarPassByRef

```
Only variables can be passed by reference at argument %d of %s()
```

This issue is emitted from the following code

```php
class F { static function f(&$v) {} } F::f('string');
```

## PhanTypeParentConstructorCalled

```
Must call parent::__construct() from %s which extends %s
```

## PhanUndeclaredTypeParameter

```
Parameter of undeclared type %s
```

This issue will be emitted from the following code

```php
function f(Undef $p) {}
```

## PhanUndeclaredTypeProperty

```
Property of undeclared type %s
```

This issue will be emitted from the following code

```php
class D { /** @var Undef */ public $p; }
```

# UndefError

## PhanEmptyFile

This low severity issue is emitted for empty files.

```
Empty file %s
```

This would be emitted if you have a file with the contents

```php
```

## PhanParentlessClass

```
Reference to parent of class %s that does not extend anything
```

This issue will be emitted from the following code

```php
class F { function f() { $v = parent::f(); } }
```

## PhanTraitParentReference

```
Reference to parent from trait %s
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
Reference to undeclared class %s
```

## PhanUndeclaredClassCatch

```
Catching undeclared class %s
```

The following code will emit this error.

```php
try {} catch (Undef $exception) {}
```

## PhanUndeclaredClassConstant

```
Reference to constant %s from undeclared class %s
```

## PhanUndeclaredClassInstanceof

```
Checking instanceof against undeclared class %s
```

This issue will be emitted from the following code

```php
$v = null;
if ($v instanceof Undef) {}
```

## PhanUndeclaredClassMethod

```
Call to method %s from undeclared class %s
```

This issue will be emitted from the following code

```php
function g(Undef $v) { $v->f(); }
```

## PhanUndeclaredClassReference

```
Reference to undeclared class %s
```

## PhanUndeclaredConstant

```
Reference to undeclared constant %s
```

## PhanUndeclaredExtendedClass

```
Class extends undeclared class %s
```

This issue will be emitted from the following code

```php
class E extends Undef {}
```

## PhanUndeclaredFunction

```
Call to undeclared function %s
```

## PhanUndeclaredInterface

```
Class implements undeclared interface %s
```

## PhanUndeclaredMethod

```
Call to undeclared method %s
```

## PhanUndeclaredProperty

```
Reference to undeclared property %s
```

This issue will be emitted from the following code

```php
class C {}
$v = (new C)->undef;
```

## PhanUndeclaredStaticMethod

```
Static call to undeclared method %s
```

This issue will be emitted from the following code

```php
C::staticMethod();
```

## PhanUndeclaredStaticProperty

```
Static property '%s' on %s is undeclared
```

## PhanUndeclaredTrait

```
Class uses undeclared trait %s
```

## PhanUndeclaredVariable

```
Variable $%s is undeclared
```

# VarError

## PhanVariableUseClause

```
Non-variables not allowed within use clause
```

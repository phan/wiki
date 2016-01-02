See [\Phan\Issue](https://github.com/etsy/phan/blob/master/src/Phan/Issue.php) for the set of error types that are emitted. Below is a listing of all issue types as of [87ab20](https://github.com/etsy/phan/tree/87ab2044f78c0263018428ee260b1b5be2646ca6/).

Please add example code, fix outdated info and add any remedies to the issues below.


# AccessError

## PhanAccessPropertyPrivate

This issue comes up when there is an attempt to access a private property outside of the scope in which its defined.

```
Cannot access private property %s
```

## PhanAccessPropertyProtected

This issue comes up when there is an attempt to access a protected property outside of the scope in which its defined or an implementing child class.

```
Cannot access protected property %s
```

# CompatError

## PhanCompatibleExpressionPHP7

This issue will be thrown if there is an expression that may be treated differently in PHP7 than it was in previous major versions of the PHP runtime. Take a look at the [PHP7 Migration Manual](http://php.net/manual/en/migration70.incompatible.php to understand changes in behavior).

```
%s expression may not be PHP 7 compatible
```

## PhanCompatiblePHP7

This issue will be thrown if there is an expression that may be treated differently in PHP7 than it was in previous major versions of the PHP runtime. Take a look at the [PHP7 Migration Manual](http://php.net/manual/en/migration70.incompatible.php to understand changes in behavior).

```
Expression may not be PHP 7 compatible
```

# Context

## PhanContextNotObject

This issue comes up when you attempt to use things like `$this` that only exist when you're inside of a class, trait or interface, but are not.

```
Cannot access %s when not in object context
```

## PhanNonStaticSelf

This issue is thrown when there is a reference to `self` in a non-static method.

```
Reference to self when not in object context
```

# DeprecatedError

## PhanDeprecatedFunction

If a class, method, function, property or constant is marked in its comment as `@deprecated`, any references to them will emit a deprecated error.

```
Call to deprecated function %s() defined at %s:%d
```

# NOOPError

## PhanNoopArray

```
Unused array
```

## PhanNoopClosure

```
Unused closure
```

## PhanNoopConstant

```
Unused constant
```

## PhanNoopProperty

```
Unused property
```

## PhanNoopVariable

```
Unused variable
```

## PhanNoopZeroReferences

```
Possibly zero references to %s
```

# ParamError

## PhanParamReqAfterOpt

```
Required argument follows optional
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

```
Call with %d arg(s) to %s() which requires %d arg(s) defined at %s:%d
```

## PhanParamTooFewInternal

```
Call with %d arg(s) to %s() which requires %d arg(s)
```

## PhanParamTooMany

```
Call with %d arg(s) to %s() which only takes %d arg(s) defined at %s:%d
```

## PhanParamTooManyInternal

```
Call with %d arg(s) to %s() which only takes %d arg(s)
```

## PhanParamTypeMismatch

```
Argument %d is %s but %s() takes %s
```

# RedefineError

## PhanRedefineClass

```
%s defined at %s:%d was previously defined as %s at %s:%d
```

## PhanRedefineClassInternal

```
%s defined at %s:%d was previously defined as %s internally
```

## PhanRedefineFunction

```
Function %s defined at %s:%d was previously defined at %s:%d
```

## PhanRedefineFunctionInternal

```
Function %s defined at %s:%d was previously defined internally
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

## PhanTypeInstantiateInterface

```
Instantiation of interface %s
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

## PhanTypeMismatchArgumentInternal

```
Argument %d (%s) is %s but %s() takes %s
```

## PhanTypeMismatchDefault

```
Default value for %s $%s can't be %s
```

## PhanTypeMismatchForeach

```
%s passed to foreach instead of array
```

## PhanTypeMismatchProperty

```
Assigning %s to property but %s is %s
```

## PhanTypeMismatchReturn

```
Returning type %s but %s() is declared to return %s
```

## PhanTypeMissingReturn

```
Method %s is declared to return %s but has no return value
```

## PhanTypeNonVarPassByRef

```
Only variables can be passed by reference at argument %d of %s()
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

```
Empty file %s
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

```
Expression is unanalyzable or feature is unimplemented. Please create an issue at https://github.com/etsy/phan/issues/new.
```

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


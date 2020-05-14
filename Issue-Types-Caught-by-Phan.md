<!-- This is mirrored at https://github.com/phan/phan/wiki/Issue-Types-Caught-by-Phan -->
<!-- The copy distributed with Phan is in the internal folder because it may be removed or moved elsewhere -->

See [\Phan\Issue](https://github.com/phan/phan/blob/master/src/Phan/Issue.php) for the most up to date list of error types that are emitted. Below is a listing of all issue types, which is periodically updated. The test case [0101_one_of_each.php](https://github.com/phan/phan/blob/master/tests/files/src/0101_one_of_each.php) was originally intended to cover all examples in this document.

A concise summary of issue categories found by Phan can be seen in [Phan's README](https://github.com/phan/phan#features).

Please add example code, fix outdated info and add any remedies to the issues below.

In addition to the below issue types, there are [additional issue types that can be detected by Phan's plugins](https://github.com/phan/phan/tree/master/.phan/plugins#plugins).

[![Gitter](https://badges.gitter.im/phan/phan.svg)](https://gitter.im/phan/phan?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

# AccessError

This category of issue is emitted when you're trying to access things that you can't access.
Note: in this document, "`@internal`" refers to user-defined elements with `/** @internal */` in their PHPDoc,
while "internal" refers to classes, functions, methods, etc.  that are built into PHP and PHP modules (e.g. `is_string`, `stdClass`, etc)

## PhanAccessClassConstantPrivate

This issue comes up when there is an attempt to access a private class constant outside of the scope in which it's defined.

```
Cannot access private class constant {CONST} defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0252_class_const_visibility.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0252_class_const_visibility.php#L17).

## PhanAccessClassConstantProtected

This issue comes up when there is an attempt to access a protected class constant outside of the scope in which it's defined.

```
Cannot access protected class constant {CONST} defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0252_class_const_visibility.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0252_class_const_visibility.php#L25).

## PhanAccessExtendsFinalClass

This issue comes up when there is an attempt to extend a user-defined final class.

```
Attempting to extend from final class {CLASS} defined at {FILE}:{LINE}
```

This will be emitted for the following code.

```php
final class FinalClass
class A extends FinalClass {}
```

## PhanAccessExtendsFinalClassInternal

This issue comes up when there is an attempt to extend an internal final class.

```
Attempting to extend from final internal class {CLASS}
```

This will be emitted for the following code.

```php
class A extends Closure {}
```

## PhanAccessMethodPrivate

This issue comes up when there is an attempt to invoke a private method outside of the scope in which it's defined.

```
Cannot access private method {METHOD} defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0175_priv_prot_methods.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0175_priv_prot_methods.php#L12).

## PhanAccessMethodPrivateWithCallMagicMethod

This issue comes up when there is an attempt to invoke a private method outside of the scope in which it's defined, but the attempt would end up calling `__call` or `__callStatic` instead.

```
Cannot access private method {METHOD} defined at {FILE}:{LINE} (if this call should be handled by __call, consider adding a @method tag to the class)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0298_call_magic_method_accesses_inaccessible.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0298_call_magic_method_accesses_inaccessible.php#L74).

## PhanAccessMethodProtected

This issue comes up when there is an attempt to invoke a protected method outside of the scope in which it's defined or an implementing child class.

```
Cannot access protected method {METHOD} defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0351_protected_constructor.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0351_protected_constructor.php#L10).

## PhanAccessMethodProtectedWithCallMagicMethod

This issue comes up when there is an attempt to invoke a protected method outside of the scope in which it's defined or an implementing child class, but the attempt would end up calling `__call` or `__callStatic` instead.

```
Cannot access protected method {METHOD} defined at {FILE}:{LINE} (if this call should be handled by __call, consider adding a @method tag to the class)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0298_call_magic_method_accesses_inaccessible.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0298_call_magic_method_accesses_inaccessible.php#L80).

## PhanAccessNonStaticToStatic

This issue is emitted when a class redeclares an inherited instance method as a static method.

```
Cannot make non static method {METHOD}() static
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0127_override_access.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0127_override_access.php#L8).

## PhanAccessNonStaticToStaticProperty

```
Cannot make non static property {PROPERTY} into the static property {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0492_class_constant_visibility.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0492_class_constant_visibility.php#L25).

## PhanAccessOverridesFinalMethod

This issue is emitted when a class attempts to override an inherited final method.

```
Declaration of method {METHOD} overrides final method {METHOD} defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0319_override_parent_and_interface.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0319_override_parent_and_interface.php#L20).

## PhanAccessOverridesFinalMethodInTrait

```
Declaration of method {METHOD} overrides final method {METHOD} defined in trait in {FILE}:{LINE}. This is actually allowed in case of traits, even for final methods, but may lead to unexpected behavior
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0318_override_final_method.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0318_override_final_method.php#L22).

## PhanAccessOverridesFinalMethodInternal

This issue is emitted when a class attempts to override an inherited final method of an internal class.

```
Declaration of method {METHOD} overrides final internal method {METHOD}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0318_override_final_method.php.expected#L8) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0318_override_final_method.php#L70).

## PhanAccessOverridesFinalMethodPHPDoc

This issue is emitted when a class declares a PHPDoc `@method` tag, despite having already inherited a final method from a base class.

```
Declaration of phpdoc method {METHOD} is an unnecessary override of final method {METHOD} defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0318_override_final_method.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0318_override_final_method.php#L49).

## PhanAccessOwnConstructor

```
Accessing own constructor directly via {CLASS}::__construct
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0310_self_construct.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0310_self_construct.php#L19).

## PhanAccessPropertyNonStaticAsStatic

This issue comes up when there is an attempt to access a non-static(instance) property as if it were a static property.

```
Accessing non static property {PROPERTY} as static
```

This will be emitted for the following code.

```php
class A { public $prop = 'value'; }
$x = A::$prop;
```

## PhanAccessPropertyPrivate

This issue comes up when there is an attempt to access a private property outside of the scope in which it's defined.

```
Cannot access private property {PROPERTY} defined at {FILE}:{LINE}
```

This will be emitted for the following code.

```php
class C1 { private static $p = 42; }
print C1::$p;
```

## PhanAccessPropertyProtected

This issue comes up when there is an attempt to access a protected property outside of the scope in which it's defined or an implementing child class.

```
Cannot access protected property {PROPERTY} defined at {FILE}:{LINE}
```

This will be emitted for the following code.

```php
class C1 { protected static $p = 42; }
print C1::$p;
```

## PhanAccessPropertyStaticAsNonStatic

This issue comes up when there is an attempt to access a static property as if it were a non static(instance) property.

```
Accessing static property {PROPERTY} as non static
```

This will be emitted for the following code.

```php
class A { public static $prop = 'value'; }
$x = (new A())->prop;
```

## PhanAccessReadOnlyMagicProperty

This is emitted when attempting to assign to magic properties declared with `@property-read`.
This does not attempt to catch all possible operations that modify magic properties.

```
Cannot modify read-only magic property {PROPERTY} defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0550_property_read_write_flags.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0550_property_read_write_flags.php#L29).

## PhanAccessReadOnlyProperty

This is emitted when attempting to read from real properties with a doc comment containing `@phan-write-only`.
This does not attempt to catch all possible operations that read magic properties.
This does not warn when the assignment is **directly** inside of the object's constructor.

```
Cannot modify read-only property {PROPERTY} defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0763_immutable_class.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0763_immutable_class.php#L20).

## PhanAccessSignatureMismatch

```
Access level to {METHOD} must be compatible with {METHOD} defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0181_override_access_level.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0181_override_access_level.php#L8).

## PhanAccessSignatureMismatchInternal

```
Access level to {METHOD} must be compatible with internal {METHOD}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0630_access_level_internal.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0630_access_level_internal.php#L3).

## PhanAccessStaticToNonStatic

This issue is emitted when a class redeclares an inherited static method as an instance method.

```
Cannot make static method {METHOD}() non static
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0625_static_to_non_static.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0625_static_to_non_static.php#L7).

## PhanAccessStaticToNonStaticProperty

```
Cannot make static property {PROPERTY} into the non static property {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0492_class_constant_visibility.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0492_class_constant_visibility.php#L26).

## PhanAccessWriteOnlyMagicProperty

This is emitted when attempting to write to magic properties declared with `@property-read`.
This does not attempt to catch all possible operations that modify properties (e.g. references, assignment operations).

```
Cannot read write-only magic property {PROPERTY} defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0550_property_read_write_flags.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0550_property_read_write_flags.php#L34).

## PhanAccessWriteOnlyProperty

This is emitted when attempting to write to real properties with a doc comment containing `@phan-read-only`.
This does not attempt to catch all possible operations that modify properties (e.g. references, assignment operations).

```
Cannot read write-only property {PROPERTY} defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0550_property_read_write_flags.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0550_property_read_write_flags.php#L44).

## PhanAccessWrongInheritanceCategory

```
Attempting to inherit {CLASSLIKE} defined at {FILE}:{LINE} as if it were a {CLASSLIKE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0316_incompatible_extend.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0316_incompatible_extend.php#L10).

## PhanAccessWrongInheritanceCategoryInternal

```
Attempting to inherit internal {CLASSLIKE} as if it were a {CLASSLIKE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0316_incompatible_extend.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0316_incompatible_extend.php#L13).

## PhanConstantAccessSignatureMismatch

```
Access level to {CONST} must be compatible with {CONST} defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0492_class_constant_visibility.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0492_class_constant_visibility.php#L34).

## PhanConstantAccessSignatureMismatchInternal

```
Access level to {CONST} must be compatible with internal {CONST}
```

## PhanConstructAccessSignatureMismatch

```
Access level to {METHOD} must be compatible with {METHOD} defined in {FILE}:{LINE} in PHP versions 7.1 and below
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0740_access_level_construct.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0740_access_level_construct.php#L8).

## PhanPropertyAccessSignatureMismatch

```
Access level to {PROPERTY} must be compatible with {PROPERTY} defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0492_class_constant_visibility.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0492_class_constant_visibility.php#L23).

## PhanPropertyAccessSignatureMismatchInternal

```
Access level to {PROPERTY} must be compatible with internal {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0607_internal_property_visibility.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0607_internal_property_visibility.php#L5).

# Analysis

This category will be emitted when Phan doesn't know how to analyze something.

Please do file an issue or otherwise get in touch if you get one of these (or an uncaught exception, or anything else that's shitty).

[![Gitter](https://badges.gitter.im/phan/phan.svg)](https://gitter.im/phan/phan?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)


## PhanInvalidConstantFQSEN

```
'{CONST}' is an invalid FQSEN for a constant
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/047_valid_define.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/047_valid_define.php#L2).

## PhanReservedConstantName

```
'{CONST}' has a reserved keyword in the constant name
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/091_redeclare_constant.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/091_redeclare_constant.php#L2).

## PhanUnanalyzable

This issue will be emitted when we hit a structure that Phan doesn't know how to parse. More commonly this will be expressed by Phan having an uncaught exception or behaving poorly.

```
Expression is unanalyzable or feature is unimplemented. Please create an issue at https://github.com/phan/phan/issues/new.
```
## PhanUnanalyzableInheritance

```
Unable to determine the method(s) which {METHOD} overrides, but Phan inferred that it did override something earlier. Please create an issue at https://github.com/phan/phan/issues/new with a test case.
```

# CompatError

This category of issue is emitted when there are compatibility issues. They will be thrown if there is an expression that may be treated differently in PHP7 than it was in previous major versions of the PHP runtime. Take a look at the [PHP7 Migration Manual](http://php.net/manual/en/migration70.incompatible.php) to understand changes in behavior.

## PhanCompatibleAnyReturnTypePHP56

```
In PHP 5.6, return types ({TYPE}) are not supported
```

## PhanCompatibleAutoload

```
Declaring an autoloader with function __autoload() was deprecated in PHP 7.2 and will become a fatal error in PHP 8.0. Use spl_autoload_register() instead (supported since PHP 5.1).
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/000_plugins.php.expected#L21) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/000_plugins.php#L64).

## PhanCompatibleDefaultEqualsNull

```
In PHP 8.0, using a default ({CODE}) that resolves to null will no longer cause the parameter ({PARAMETER}) to be nullable
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0782_nullable_compat.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0782_nullable_compat.php#L4).

## PhanCompatibleDimAlternativeSyntax

This is emitted deliberately when using the polyfill and/or using php 7.4+.

```
Array and string offset access syntax with curly braces is deprecated in PHP 7.4. Use square brackets instead. Seen for {CODE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/062_test.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/062_test.php#L2).

## PhanCompatibleExpressionPHP7

This issue will be thrown if there is an expression that may be treated differently in PHP7 than it was in previous major versions of the PHP runtime. Take a look at the [PHP7 Migration Manual](http://php.net/manual/en/migration70.incompatible.php) to understand changes in behavior.

The config `backward_compatibility_checks` must be enabled for this to run such as by passing the command line argument `--backward-compatibility-checks` or by defining it in a `.phan/config.php` file such as [Phan's own config](https://github.com/phan/phan/blob/2.4.1/.phan/config.php).

```
{CLASS} expression may not be PHP 7 compatible
```

## PhanCompatibleImplodeOrder

```
In php 7.4, passing glue string after the array is deprecated for {FUNCTION}. Should this swap the parameters of type {TYPE} and {TYPE}?
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0511_implode.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0511_implode.php#L7).

## PhanCompatibleIterableTypePHP70

```
Return type '{TYPE}' means a Traversable/array value starting in PHP 7.1. In PHP 7.0, iterable refers to a class/interface with the name 'iterable'
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/007_use.php.expected#L7) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/007_use.php#L7).

## PhanCompatibleKeyedArrayAssignPHP70

```
Using array keys in an array destructuring assignment is not compatible with PHP 7.0
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/003_short_array.php.expected#L7) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/003_short_array.php#L21).

## PhanCompatibleMultiExceptionCatchPHP70

```
Catching multiple exceptions is not supported before PHP 7.1
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/008_catch_multiple_exceptions.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/008_catch_multiple_exceptions.php#L5).

## PhanCompatibleNegativeStringOffset

```
Using negative string offsets is not supported before PHP 7.1 (emits an 'Uninitialized string offset' notice)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/009_negative_string_offset.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/009_negative_string_offset.php#L5).

## PhanCompatibleNullableTypePHP70

```
Nullable type '{TYPE}' is not compatible with PHP 7.0
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/014_union_type_invalid.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/014_union_type_invalid.php#L3).

## PhanCompatibleObjectTypePHP71

```
Type '{TYPE}' refers to any object starting in PHP 7.2. In PHP 7.1 and earlier, it refers to a class/interface with the name 'object'
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/007_use.php.expected#L8) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/007_use.php#L7).

## PhanCompatiblePHP7

This issue will be thrown if there is an expression that may be treated differently in PHP7 than it was in previous major versions of the PHP runtime. Take a look at the [PHP7 Migration Manual](http://php.net/manual/en/migration70.incompatible.php) to understand changes in behavior.

The config `backward_compatibility_checks` must be enabled for this to run such as by passing the command line argument `--backward-compatibility-checks` or by defining it in a `.phan/config.php` file such as [Phan's own config](https://github.com/phan/phan/blob/2.4.1/.phan/config.php).

```
Expression may not be PHP 7 compatible
```

This will be emitted for the following code.

```php
$c->$m[0]();
```

## PhanCompatiblePHP8PHP4Constructor

```
PHP4 constructors will be removed in php 8, and should not be used. __construct() should be added/used instead to avoid accidentally calling {METHOD}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0198_list_property.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0198_list_property.php#L7).

## PhanCompatibleScalarTypePHP56

```
In PHP 5.6, scalar types such as {TYPE} in type signatures are treated like class names
```

## PhanCompatibleShortArrayAssignPHP70

```
Square bracket syntax for an array destructuring assignment is not compatible with PHP 7.0
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/003_short_array.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/003_short_array.php#L8).

## PhanCompatibleStaticType

```
Cannot use static return types before php 8.0
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/013_union_type_errors.php.expected#L9) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/013_union_type_errors.php#L13).

## PhanCompatibleSyntaxNotice

This is used for notices that are emitted while Phan is parsing with the native parser.
Currently, this only catches the notice about the `(real)` cast from the native parser in php 7.4.

```
Saw a parse notice: {DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/expected/012_real_cast.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/src/012_real_cast.php#L2).

## PhanCompatibleThrowExpression

```
Cannot use throw as an expression before php 8.0 in {CODE}
```

## PhanCompatibleTypedProperty

```
Cannot use typed properties before php 7.4. This property group has type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/011_typed_properties.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/011_typed_properties.php#L3).

## PhanCompatibleUnionType

```
Cannot use union types ({TYPE}) before php 8.0
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/013_union_type_errors.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/013_union_type_errors.php#L2).

## PhanCompatibleUnparenthesizedTernary

```
Unparenthesized '{CODE}' is deprecated. Use either '{CODE}' or '{CODE}'
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/expected/013_ambiguous_ternary.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/src/013_ambiguous_ternary.php#L6).

## PhanCompatibleUnsetCast

```
The unset cast (in {CODE}) was deprecated in PHP 7.2 and will become a fatal error in PHP 8.0.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/061_cast_crash.php.expected#L11) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/061_cast_crash.php#L45).

## PhanCompatibleUseIterablePHP71

```
Using '{TYPE}' as iterable will be a syntax error in PHP 7.2 (iterable becomes a native type with subtypes Array and Iterator).
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/007_use.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/007_use.php#L4).

## PhanCompatibleUseObjectPHP71

```
Using '{TYPE}' as object will be a syntax error in PHP 7.2 (object becomes a native type that accepts any class instance).
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/007_use.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/007_use.php#L5).

## PhanCompatibleUseVoidPHP70

```
Using '{TYPE}' as void will be a syntax error in PHP 7.1 (void becomes the absence of a return type).
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/007_use.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/007_use.php#L3).

## PhanCompatibleVoidTypePHP70

```
Return type '{TYPE}' means the absence of a return value starting in PHP 7.1. In PHP 7.0, void refers to a class/interface with the name 'void'
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/004_void.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/004_void.php#L4).

## PhanThrowCommentInToString

```
{FUNCTIONLIKE} documents that it throws {TYPE}, but throwing in __toString() is a fatal error prior to PHP 7.4
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/133_throw_in_to_string.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/133_throw_in_to_string.php#L6).

## PhanThrowStatementInToString

```
{FUNCTIONLIKE} throws {TYPE} here, but throwing in __toString() is a fatal error prior to PHP 7.4
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/133_throw_in_to_string.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/133_throw_in_to_string.php#L7).

# Context

This category of issue is for when you're doing stuff out of the context in which you're allowed to do it, e.g. referencing `self` or `parent` when not in a class, interface or trait.

## PhanContextNotObject

This issue comes up when you attempt to use things like `$this` that only exist when you're inside of a class, trait or interface, but are not.

```
Cannot access {CLASS} when not in object context
```

This will be emitted for the following code.

```php
new parent;
```

## PhanContextNotObjectInCallable

```
Cannot access {CLASS} when not in object context, but code is using callable {METHOD}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0370_callable_edge_cases.php.expected#L8) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0370_callable_edge_cases.php#L47).

## PhanContextNotObjectUsingSelf

```
Cannot use {CLASS} as type when not in object context in {FUNCTION}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/034_function_return_self.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/034_function_return_self.php#L3).

## PhanSuspiciousMagicConstant

```
Suspicious reference to magic constant {CODE}: {DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0594_magic_constant.php.expected#L10) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0594_magic_constant.php#L8).

# DeprecatedError

This category of issue comes up when you're accessing deprecated elements (as marked by the `@deprecated` comment).

**Note!** Only classes, traits, interfaces, methods, functions, properties, and traits may be marked as deprecated. You can't deprecate a variable or any other expression.

## PhanDeprecatedCaseInsensitiveDefine

```
Creating case-insensitive constants with define() has been deprecated in PHP 7.3
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0589_case_insensitive_define.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0589_case_insensitive_define.php#L2).

## PhanDeprecatedClass

```
Using a deprecated class {CLASS} defined at {FILE}:{LINE}{DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0123_deprecated_class.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0123_deprecated_class.php#L12).

## PhanDeprecatedClassConstant

```
Reference to deprecated property {PROPERTY} defined at {FILE}:{LINE}{DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php72_files/expected/0007_deprecated_class_constant.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php72_files/src/0007_deprecated_class_constant.php#L6).

## PhanDeprecatedFunction

If a class, method, function, property or constant is marked in its comment as `@deprecated`, any references to them will emit a deprecated error.

```
Call to deprecated function {FUNCTIONLIKE} defined at {FILE}:{LINE}{DETAILS}
```

This will be emitted for the following code.

```php
/** @deprecated  */
function f1() {}
f1();
```

## PhanDeprecatedFunctionInternal

```
Call to deprecated function {FUNCTIONLIKE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php72_files/expected/0006_deprecated_create_internal_function.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php72_files/src/0006_deprecated_create_internal_function.php#L4).

## PhanDeprecatedInterface

```
Using a deprecated interface {INTERFACE} defined at {FILE}:{LINE}{DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0269_deprecated_interface.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0269_deprecated_interface.php#L5).

## PhanDeprecatedProperty

```
Reference to deprecated property {PROPERTY} defined at {FILE}:{LINE}{DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0171_deprecated_property.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0171_deprecated_property.php#L9).

## PhanDeprecatedTrait

```
Using a deprecated trait {TRAIT} defined at {FILE}:{LINE}{DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0270_deprecated_trait.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0270_deprecated_trait.php#L5).

# NOOPError

Issues in this category are emitted when you have reasonable code but it isn't doing anything. They're all low severity.

## PhanEmptyClosure

These `PhanEmpty*` issues warn about empty statement lists of functions, and are emitted by `EmptyMethodAndFunctionPlugin`.

Note that this is not emitted for empty statement lists in functions or methods that are overrides, are overridden, or are deprecated.

```
Empty closure
```

## PhanEmptyForeach

```
Saw a foreach statement with empty iterable type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0112_foreach_with_skipped_list.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0112_foreach_with_skipped_list.php#L3).

## PhanEmptyForeachBody

```
Saw a foreach statement with empty body over array of type {TYPE} (iterating has no side effects)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0112_foreach_with_skipped_list.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0112_foreach_with_skipped_list.php#L3).

## PhanEmptyFunction

```
Empty function {FUNCTION}
```

## PhanEmptyPrivateMethod

```
Empty private method {METHOD}
```

## PhanEmptyProtectedMethod

```
Empty protected method {METHOD}
```

## PhanEmptyPublicMethod

```
Empty public method {METHOD}
```

## PhanEmptyYieldFrom

```
Saw a yield from statement with empty iterable type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0774_empty_foreach.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0774_empty_foreach.php#L16).

## PhanNoopArray

Emitted when you have an array that is not used in any way.

```
Unused array
```

This will be emitted for the following code.

```php
[1,2,3];
```

## PhanNoopArrayAccess

```
Unused array offset fetch
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/062_test.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/062_test.php#L2).

## PhanNoopBinaryOperator

```
Unused result of a binary '{OPERATOR}' operator
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/079_both_booleans.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/079_both_booleans.php#L5).

## PhanNoopCast

```
Unused result of a ({TYPE})({CODE}) cast
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0620_more_noop_expressions.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0620_more_noop_expressions.php#L7).

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

## PhanNoopEmpty

```
Unused result of an empty({CODE}) check
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0620_more_noop_expressions.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0620_more_noop_expressions.php#L3).

## PhanNoopEncapsulatedStringLiteral

```
Unused result of an encapsulated string literal
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0470_noop_scalar.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0470_noop_scalar.php#L4).

## PhanNoopIsset

```
Unused result of an isset({CODE}) check
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/011_isset_intrinsic_expression5.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/011_isset_intrinsic_expression5.php#L2).

## PhanNoopNew

NOTE: by adding `@phan-constructor-used-for-side-effects` to the doc comment of the class-like being used, `PhanNoopNew` can be suppressed on uses of that class.

```
Unused result of new object creation expression in {CODE} (this may be called for the side effects of the non-empty constructor or destructor)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0803_noop_new.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0803_noop_new.php#L4).

## PhanNoopNewNoSideEffects

```
Unused result of new object creation expression in {CODE} (this is likely free of side effects - there is no known non-empty constructor or destructor)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0270_deprecated_trait.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0270_deprecated_trait.php#L7).

## PhanNoopNumericLiteral

```
Unused result of a numeric literal {SCALAR} near this line
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/072_assign.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/072_assign.php#L1).

## PhanNoopProperty

Emitted when you have a reference to a property that is unused.

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

## PhanNoopStringLiteral

```
Unused result of a string literal {STRING_LITERAL} near this line
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/051_invalid_function_node.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/051_invalid_function_node.php#L3).

## PhanNoopSwitchCases

```
This switch statement only has the default case
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0870_noop_switch.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0870_noop_switch.php#L3).

## PhanNoopTernary

```
Unused result of a ternary expression where the true/false results don't seen to have side effects
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0740_noop_ternary.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0740_noop_ternary.php#L3).

## PhanNoopUnaryOperator

```
Unused result of a unary '{OPERATOR}' operator
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0422_unary_noop.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0422_unary_noop.php#L3).

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

## PhanReadOnlyPHPDocProperty

```
Possibly zero write references to PHPDoc @property {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/108_magic_property_unreferenced.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/108_magic_property_unreferenced.php#L9).

## PhanReadOnlyPrivateProperty

These issues are emitted when the analyzed file list contains at least one read operation
for a given declared property, but no write operations on that property.

There may be false positives if dynamic property accesses are performed, or if the code is a library that is used elsewhere.

```
Possibly zero write references to private property {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/084_read_only_property.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/084_read_only_property.php#L4).

## PhanReadOnlyProtectedProperty

```
Possibly zero write references to protected property {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/084_read_only_property.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/084_read_only_property.php#L5).

## PhanReadOnlyPublicProperty

```
Possibly zero write references to public property {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/048_redundant_binary_op.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/048_redundant_binary_op.php#L5).

## PhanRedundantArrayValuesCall

```
Attempting to convert {TYPE} to a list using {FUNCTION} (it is already a list)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/164_array_values_redundant.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/164_array_values_redundant.php#L3).

## PhanShadowedVariableInArrowFunc

```
Short arrow function shadows variable ${VARIABLE} from the outer scope
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/expected/004_arrow_func_shadow.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/src/004_arrow_func_shadow.php#L7).

## PhanSideEffectFreeDoWhileBody

**Note that `PhanSideEffectFree...` issue types rely on `--unused-variable-detection`, and will not run in the global scope as a result.**
Also note that the plugin `UseReturnValuePlugin` and its `plugin_config` setting `infer_pure_methods` should be enabled,
for this to warn about loops containing function and method calls.

```
Saw a do-while loop which probably has no side effects
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/172_infer_pure_useless_loop.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/172_infer_pure_useless_loop.php#L24).

## PhanSideEffectFreeForBody

```
Saw a for loop which probably has no side effects
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/119_increment_decrement_unused.php.expected#L9) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/119_increment_decrement_unused.php#L22).

## PhanSideEffectFreeForeachBody

Also, this issue will not be emitted if the expression being iterated over is possibly an object (of any class) or has an unknown real type.

```
Saw a foreach loop which probably has no side effects
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0189_2d_array.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0189_2d_array.php#L7).

## PhanSideEffectFreeWhileBody

```
Saw a while loop which probably has no side effects
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/056_while_loop.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/056_while_loop.php#L4).

## PhanSuspiciousBinaryAddLists

```
Addition of {TYPE} + {TYPE} {CODE} is a suspicious way to add two lists. Some of the array fields from the left hand side will be part of the result, replacing the fields with the same key from the right hand side (this operator does not concatenate the lists)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0838_globals_assign_op.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0838_globals_assign_op.php#L4).

## PhanUnreachableCatch

```
Catch statement for {CLASSLIKE} is unreachable. An earlier catch statement at line {LINE} caught the ancestor class/interface {CLASSLIKE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0462_unreachable_catch.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0462_unreachable_catch.php#L9).

## PhanUnreferencedClass

Similar issues exist for PhanUnreferencedProperty, PhanUnreferencedConstant, PhanUnreferencedMethod, and PhanUnreferencedFunction

This issue is disabled by default, but can be enabled by setting `dead_code_detection` to enabled. It indicates that the given element is (possibly) unused.

```
Possibly zero references to class {CLASS}
```

This will be emitted for the following code so long as `dead_code_detection` is enabled.

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

## PhanUnreferencedClosure

```
Possibly zero references to {FUNCTION}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/017_unreferenced_closure.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/017_unreferenced_closure.php#L10).

## PhanUnreferencedConstant

```
Possibly zero references to global constant {CONST}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/063_unused_dynamic_constant.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/063_unused_dynamic_constant.php#L8).

## PhanUnreferencedFunction

```
Possibly zero references to function {FUNCTION}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/155_use_return_value_check_defining.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/155_use_return_value_check_defining.php#L2).

## PhanUnreferencedPHPDocProperty

```
Possibly zero references to PHPDoc @property {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/108_magic_property_unreferenced.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/108_magic_property_unreferenced.php#L5).

## PhanUnreferencedPrivateClassConstant

```
Possibly zero references to private class constant {CONST}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/083_unreferenced_class_element.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/083_unreferenced_class_element.php#L4).

## PhanUnreferencedPrivateMethod

```
Possibly zero references to private method {METHOD}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/083_unreferenced_class_element.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/083_unreferenced_class_element.php#L7).

## PhanUnreferencedPrivateProperty

```
Possibly zero references to private property {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/012_typed_properties_errors.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/012_typed_properties_errors.php#L8).

## PhanUnreferencedProtectedClassConstant

```
Possibly zero references to protected class constant {CONST}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/083_unreferenced_class_element.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/083_unreferenced_class_element.php#L5).

## PhanUnreferencedProtectedMethod

```
Possibly zero references to protected method {METHOD}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/015_trait_method.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/015_trait_method.php#L9).

## PhanUnreferencedProtectedProperty

```
Possibly zero references to protected property {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/083_unreferenced_class_element.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/083_unreferenced_class_element.php#L6).

## PhanUnreferencedPublicClassConstant

```
Possibly zero references to public class constant {CONST}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/021_param_default.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/021_param_default.php#L5).

## PhanUnreferencedPublicMethod

```
Possibly zero references to public method {METHOD}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/092_isset_plugin_hang.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/092_isset_plugin_hang.php#L7).

## PhanUnreferencedPublicProperty

```
Possibly zero references to public property {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/027_native_syntax_check.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/027_native_syntax_check.php#L3).

## PhanUnreferencedUseConstant

```
Possibly zero references to use statement for constant {CONST} ({CONST})
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0268_group_use.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0268_group_use.php#L4).

## PhanUnreferencedUseFunction

```
Possibly zero references to use statement for function {FUNCTION} ({FUNCTION})
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0268_group_use.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0268_group_use.php#L3).

## PhanUnreferencedUseNormal

```
Possibly zero references to use statement for classlike/namespace {CLASSLIKE} ({CLASSLIKE})
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0268_group_use.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0268_group_use.php#L2).

## PhanUnusedClosureParameter

Phan has various checks (See the `unused_variable_detection` config)
to detect if a variable or parameter is unused.

```
Parameter ${PARAMETER} is never used
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/006_preg_regex.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/006_preg_regex.php#L12).

## PhanUnusedClosureUseVariable

```
Closure use variable ${VARIABLE} is never used
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0012_closures.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0012_closures.php#L13).

## PhanUnusedGlobalFunctionParameter

```
Parameter ${PARAMETER} is never used
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/010_functions8.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/010_functions8.php#L2).

## PhanUnusedGotoLabel

Note: Phan does not understand the effects of "goto" on control flow.

Phan also does not check for missing "goto" labels - This can be done with `InvokePHPNativeSyntaxCheckPlugin`

```
Unused goto label {CODE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0648_goto_label_unused.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0648_goto_label_unused.php#L3).

## PhanUnusedPrivateFinalMethodParameter

```
Parameter ${PARAMETER} is never used
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/082_unused_parameter.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/082_unused_parameter.php#L10).

## PhanUnusedPrivateMethodParameter

```
Parameter ${PARAMETER} is never used
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0127_override_access.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0127_override_access.php#L12).

## PhanUnusedProtectedFinalMethodParameter

```
Parameter ${PARAMETER} is never used
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/082_unused_parameter.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/082_unused_parameter.php#L7).

## PhanUnusedProtectedMethodParameter

```
Parameter ${PARAMETER} is never used
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0056_aggressive_return_types.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0056_aggressive_return_types.php#L3).

## PhanUnusedProtectedNoOverrideMethodParameter

```
Parameter ${PARAMETER} is never used
```

## PhanUnusedPublicFinalMethodParameter

```
Parameter ${PARAMETER} is never used
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/082_unused_parameter.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/082_unused_parameter.php#L4).

## PhanUnusedPublicMethodParameter

```
Parameter ${PARAMETER} is never used
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/004_unused_suppression_plugin.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/004_unused_suppression_plugin.php#L15).

## PhanUnusedPublicNoOverrideMethodParameter

```
Parameter ${PARAMETER} is never used
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/167_trait_crash.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/167_trait_crash.php#L9).

## PhanUnusedReturnBranchWithoutSideEffects

```
Possibly useless branch in a function where the return value must be used - all branches return values equivalent to {CODE} (previous return is at line {LINE})
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/160_useless_return.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/160_useless_return.php#L12).

## PhanUnusedVariable

Phan has various checks (See the `unused_variable_detection` config)
to detect if a variable or parameter is unused.

```
Unused definition of variable ${VARIABLE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/037_assign_op.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/037_assign_op.php#L3).

## PhanUnusedVariableCaughtException

```
Unused definition of variable ${VARIABLE} as a caught exception
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/054_shadowed_exception.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/054_shadowed_exception.php#L6).

## PhanUnusedVariableGlobal

```
Unreferenced definition of variable ${VARIABLE} as a global variable
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0676_unused_global.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0676_unused_global.php#L4).

## PhanUnusedVariableReference

```
Unused definition of variable ${VARIABLE} as a reference
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/122_closure_reference.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/122_closure_reference.php#L21).

## PhanUnusedVariableStatic

```
Unreferenced definition of variable ${VARIABLE} as a static variable
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0669_invalid_static.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0669_invalid_static.php#L6).

## PhanUnusedVariableValueOfForeachWithKey

```
Unused definition of variable ${VARIABLE} as the value of a foreach loop that included keys
```


## PhanUseConstantNoEffect

NOTE: this deliberately warns only about use statements in the global namespace,
and not for `namespace MyNs; use function MyNs\PHP_VERSION_ID;`,
which does have an effect of preventing the fallback to the global constant.

```
The use statement for constant {CONST} has no effect
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0553_unreferenced_use.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0553_unreferenced_use.php#L4).

## PhanUseFunctionNoEffect

NOTE: this deliberately warns only about use statements in the global namespace,
and not for `namespace MyNs; use function MyNs\is_string;`,
which does have an effect of preventing the fallback to the global function.

```
The use statement for function {FUNCTION} has no effect
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0553_unreferenced_use.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0553_unreferenced_use.php#L6).

## PhanUseNormalNamespacedNoEffect

Note: `warn_about_redundant_use_namespaced_class` must be enabled for this to be detected.

```
The use statement for class/namespace {CLASS} in a namespace has no effect
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0553_unreferenced_use.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0553_unreferenced_use.php#L28).

## PhanUseNormalNoEffect

```
The use statement for class/namespace {CLASS} in the global namespace has no effect
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0564_global_namespace_functions_constants.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0564_global_namespace_functions_constants.php#L4).

## PhanUselessBinaryAddRight

```
Addition of {TYPE} + {TYPE} {CODE} is probably unnecessary. Array fields from the left hand side will be used instead of each of the fields from the right hand side
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0146_array_concat.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0146_array_concat.php#L5).

## PhanVariableDefinitionCouldBeConstant

The `PhanVariableDefinitionCouldBe*` issues are detected by `--constant-variable-detection`.
They are almost entirely false positives in most coding styles, but may pick up some code that can be cleaned up.

```
Uses of ${VARIABLE} could probably be replaced with a literal or constant
```

## PhanVariableDefinitionCouldBeConstantEmptyArray

```
Uses of ${VARIABLE} could probably be replaced with an empty array
```

## PhanVariableDefinitionCouldBeConstantFalse

```
Uses of ${VARIABLE} could probably be replaced with false or a named constant
```

## PhanVariableDefinitionCouldBeConstantFloat

```
Uses of ${VARIABLE} could probably be replaced with a literal or constant float
```

## PhanVariableDefinitionCouldBeConstantInt

```
Uses of ${VARIABLE} could probably be replaced with literal integer or a named constant
```

## PhanVariableDefinitionCouldBeConstantNull

```
Uses of ${VARIABLE} could probably be replaced with null or a named constant
```

## PhanVariableDefinitionCouldBeConstantString

```
Uses of ${VARIABLE} could probably be replaced with a literal or constant string
```

## PhanVariableDefinitionCouldBeConstantTrue

```
Uses of ${VARIABLE} could probably be replaced with true or a named constant
```

## PhanWriteOnlyPHPDocProperty

```
Possibly zero read references to PHPDoc @property {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/108_magic_property_unreferenced.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/108_magic_property_unreferenced.php#L8).

## PhanWriteOnlyPrivateProperty

```
Possibly zero read references to private property {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/024_strict_property_assignment.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/024_strict_property_assignment.php#L5).

## PhanWriteOnlyProtectedProperty

```
Possibly zero read references to protected property {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/024_strict_property_assignment.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/024_strict_property_assignment.php#L8).

## PhanWriteOnlyPublicProperty

```
Possibly zero read references to public property {PROPERTY}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/024_strict_property_assignment.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/024_strict_property_assignment.php#L11).

# ParamError

This category of error comes up when you're messing up your method or function parameters in some way.

## PhanParamMustBeUserDefinedClassname

```
First argument of class_alias() must be a name of user defined class ('{CLASS}' attempted)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0615_class_alias.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0615_class_alias.php#L3).

## PhanParamRedefined

```
Redefinition of parameter {PARAMETER}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0183_redefined_parameter.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0183_redefined_parameter.php#L2).

## PhanParamReqAfterOpt


```
Required parameter {PARAMETER} follows optional {PARAMETER}
```

This will be emitted for the following code

```php
function f2($p1 = null, $p2) {}
```

## PhanParamSignatureMismatch

This compares the param and return types inferred from phpdoc and real types,
and warns if an overriding method's signature is incompatible with the overridden method.
**For a check with much lower false positives and clearer issue messages, use the `PhanParamSignatureRealMismatch...` issue types instead.**

```
Declaration of {METHOD} should be compatible with {METHOD} defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0869_param_default.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0869_param_default.php#L7).

## PhanParamSignatureMismatchInternal

This compares the param and return types inferred from phpdoc and real types (as well as documentation of internal methods),
and warns if an overriding method's signature is incompatible with the overridden internal method.
For a check with much lower false positives and clearer issue messages, use the `PhanParamSignatureRealMismatchInternal...` issue types.

```
Declaration of {METHOD} should be compatible with internal {METHOD}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0308_inheritdoc_incompatible.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0308_inheritdoc_incompatible.php#L7).

## PhanParamSignaturePHPDocMismatchHasNoParamType

```
Declaration of real/@method {METHOD} should be compatible with real/@method {METHOD} (parameter #{INDEX} with no type cannot replace original parameter with type '{TYPE}') defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0627_signature_mismatch.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0627_signature_mismatch.php#L20).

## PhanParamSignaturePHPDocMismatchHasParamType

```
Declaration of real/@method {METHOD} should be compatible with real/@method {METHOD} (parameter #{INDEX} has type '{TYPE}' which cannot replace original parameter with no type) defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0627_signature_mismatch.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0627_signature_mismatch.php#L19).

## PhanParamSignaturePHPDocMismatchParamIsNotReference

```
Declaration of real/@method {METHOD} should be compatible with real/@method {METHOD} (parameter #{INDEX} is a non-reference parameter overriding a reference parameter) defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0627_signature_mismatch.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0627_signature_mismatch.php#L21).

## PhanParamSignaturePHPDocMismatchParamIsReference

```
Declaration of real/@method {METHOD} should be compatible with real/@method {METHOD} (parameter #{INDEX} is a reference parameter overriding a non-reference parameter) defined in {FILE}:{LINE}
```

## PhanParamSignaturePHPDocMismatchParamNotVariadic

```
Declaration of real/@method {METHOD} should be compatible with real/@method {METHOD} (parameter #{INDEX} is a non-variadic parameter replacing a variadic parameter) defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0627_signature_mismatch.php.expected#L10) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0627_signature_mismatch.php#L24).

## PhanParamSignaturePHPDocMismatchParamType

```
Declaration of real/@method {METHOD} should be compatible with real/@method {METHOD} (parameter #{INDEX} of type '{TYPE}' cannot replace original parameter of type '{TYPE}') defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0315_magic_method_compat.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0315_magic_method_compat.php#L15).

## PhanParamSignaturePHPDocMismatchParamVariadic

```
Declaration of real/@method {METHOD} should be compatible with real/@method {METHOD} (parameter #{INDEX} is a variadic parameter replacing a non-variadic parameter) defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0627_signature_mismatch.php.expected#L12) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0627_signature_mismatch.php#L25).

## PhanParamSignaturePHPDocMismatchReturnType

```
Declaration of real/@method {METHOD} should be compatible with real/@method {METHOD} (method returning '{TYPE}' cannot override method returning '{TYPE}') defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0627_signature_mismatch.php.expected#L14) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0627_signature_mismatch.php#L26).

## PhanParamSignaturePHPDocMismatchTooFewParameters

```
Declaration of real/@method {METHOD} should be compatible with real/@method {METHOD} (the method override accepts {COUNT} parameter(s), but the overridden method can accept {COUNT}) defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0627_signature_mismatch.php.expected#L16) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0627_signature_mismatch.php#L27).

## PhanParamSignaturePHPDocMismatchTooManyRequiredParameters

```
Declaration of real/@method {METHOD} should be compatible with real/@method {METHOD} (the method override requires {COUNT} parameter(s), but the overridden method requires only {COUNT}) defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0315_magic_method_compat.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0315_magic_method_compat.php#L13).

## PhanParamSignatureRealMismatchHasNoParamType

```
Declaration of {METHOD} should be compatible with {METHOD} (parameter #{INDEX} with no type cannot replace original parameter with type '{TYPE}') defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0126_override_signature.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0126_override_signature.php#L12).

## PhanParamSignatureRealMismatchHasNoParamTypeInternal

```
Declaration of {METHOD} should be compatible with internal {METHOD} (parameter #{INDEX} with no type cannot replace original parameter with type '{TYPE}')
```

## PhanParamSignatureRealMismatchHasParamType

```
Declaration of {METHOD} should be compatible with {METHOD} (parameter #{INDEX} has type '{TYPE}' which cannot replace original parameter with no type) defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0374_compat.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0374_compat.php#L42).

## PhanParamSignatureRealMismatchHasParamTypeInternal

```
Declaration of {METHOD} should be compatible with internal {METHOD} (parameter #{INDEX} has type '{TYPE}' which cannot replace original parameter with no type)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0631_internal_signature_mismatch.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0631_internal_signature_mismatch.php#L9).

## PhanParamSignatureRealMismatchParamIsNotReference

```
Declaration of {METHOD} should be compatible with {METHOD} (parameter #{INDEX} is a non-reference parameter overriding a reference parameter) defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0124_override_signature.php.expected#L19) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0124_override_signature.php#L68).

## PhanParamSignatureRealMismatchParamIsNotReferenceInternal

```
Declaration of {METHOD} should be compatible with internal {METHOD} (parameter #{INDEX} is a non-reference parameter overriding a reference parameter)
```

## PhanParamSignatureRealMismatchParamIsReference

```
Declaration of {METHOD} should be compatible with {METHOD} (parameter #{INDEX} is a reference parameter overriding a non-reference parameter) defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0124_override_signature.php.expected#L17) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0124_override_signature.php#L67).

## PhanParamSignatureRealMismatchParamIsReferenceInternal

```
Declaration of {METHOD} should be compatible with internal {METHOD} (parameter #{INDEX} is a reference parameter overriding a non-reference parameter)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0631_internal_signature_mismatch.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0631_internal_signature_mismatch.php#L21).

## PhanParamSignatureRealMismatchParamNotVariadic

```
Declaration of {METHOD} should be compatible with {METHOD} (parameter #{INDEX} is a non-variadic parameter replacing a variadic parameter) defined in {FILE}:{LINE}
```

## PhanParamSignatureRealMismatchParamNotVariadicInternal

```
Declaration of {METHOD} should be compatible with internal {METHOD} (parameter #{INDEX} is a non-variadic parameter replacing a variadic parameter)
```

## PhanParamSignatureRealMismatchParamType

```
Declaration of {METHOD} should be compatible with {METHOD} (parameter #{INDEX} of type '{TYPE}' cannot replace original parameter of type '{TYPE}') defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0126_override_signature.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0126_override_signature.php#L16).

## PhanParamSignatureRealMismatchParamTypeInternal

```
Declaration of {METHOD} should be compatible with internal {METHOD} (parameter #{INDEX} of type '{TYPE}' cannot replace original parameter of type '{TYPE}')
```

## PhanParamSignatureRealMismatchParamVariadic

```
Declaration of {METHOD} should be compatible with {METHOD} (parameter #{INDEX} is a variadic parameter replacing a non-variadic parameter) defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0279_should_check_variadic_mismatch.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0279_should_check_variadic_mismatch.php#L21).

## PhanParamSignatureRealMismatchParamVariadicInternal

```
Declaration of {METHOD} should be compatible with internal {METHOD} (parameter #{INDEX} is a variadic parameter replacing a non-variadic parameter)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0631_internal_signature_mismatch.php.expected#L7) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0631_internal_signature_mismatch.php#L27).

## PhanParamSignatureRealMismatchReturnType

```
Declaration of {METHOD} should be compatible with {METHOD} (method returning '{TYPE}' cannot override method returning '{TYPE}') defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0278_should_differentiate_phpdoc_return_type.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0278_should_differentiate_phpdoc_return_type.php#L10).

## PhanParamSignatureRealMismatchReturnTypeInternal

```
Declaration of {METHOD} should be compatible with internal {METHOD} (method returning '{TYPE}' cannot override method returning '{TYPE}')
```

## PhanParamSignatureRealMismatchTooFewParameters

```
Declaration of {METHOD} should be compatible with {METHOD} (the method override accepts {COUNT} parameter(s), but the overridden method can accept {COUNT}) defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0227_trait_class_interface.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0227_trait_class_interface.php#L13).

## PhanParamSignatureRealMismatchTooFewParametersInternal

```
Declaration of {METHOD} should be compatible with internal {METHOD} (the method override accepts {COUNT} parameter(s), but the overridden method can accept {COUNT})
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0631_internal_signature_mismatch.php.expected#L9) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0631_internal_signature_mismatch.php#L33).

## PhanParamSignatureRealMismatchTooManyRequiredParameters

```
Declaration of {METHOD} should be compatible with {METHOD} (the method override requires {COUNT} parameter(s), but the overridden method requires only {COUNT}) defined in {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0869_param_default.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0869_param_default.php#L7).

## PhanParamSignatureRealMismatchTooManyRequiredParametersInternal

```
Declaration of {METHOD} should be compatible with internal {METHOD} (the method override requires {COUNT} parameter(s), but the overridden method requires only {COUNT})
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0631_internal_signature_mismatch.php.expected#L11) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0631_internal_signature_mismatch.php#L39).

## PhanParamSpecial1

```
Argument {INDEX} (${PARAMETER}) is {TYPE} but {FUNCTIONLIKE} takes {TYPE} when argument {INDEX} is {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0511_implode.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0511_implode.php#L8).

## PhanParamSpecial2

```
Argument {INDEX} (${PARAMETER}) is {TYPE} but {FUNCTIONLIKE} takes {TYPE} when passed only one argument
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0511_implode.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0511_implode.php#L4).

## PhanParamSpecial3

```
The last argument to {FUNCTIONLIKE} must be of type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0101_one_of_each.php.expected#L18) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0101_one_of_each.php#L57).

## PhanParamSpecial4

```
The second to last argument to {FUNCTIONLIKE} must be of type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0101_one_of_each.php.expected#L20) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0101_one_of_each.php#L60).

## PhanParamSuspiciousOrder

```
Argument #{INDEX} of this call to {FUNCTIONLIKE} is typically a literal or constant but isn't, but argument #{INDEX} (which is typically a variable) is a literal or constant. The arguments may be in the wrong order.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0381_wrong_order.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0381_wrong_order.php#L4).

## PhanParamTooFew

This issue indicates that you're not passing in at least the number of required parameters to a function or method.

```
Call with {COUNT} arg(s) to {FUNCTIONLIKE} which requires {COUNT} arg(s) defined at {FILE}:{LINE}
```

This will be emitted for the code

```php
function f6($i) {}
f6();
```

## PhanParamTooFewCallable

```
Call with {COUNT} arg(s) to {FUNCTIONLIKE} (as a provided callable) which requires {COUNT} arg(s) defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/033_closure_crash.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/033_closure_crash.php#L2).

## PhanParamTooFewInternal

This issue indicates that you're not passing in at least the number of required parameters to an internal function or method.

```
Call with {COUNT} arg(s) to {FUNCTIONLIKE} which requires {COUNT} arg(s)
```

This will be emitted for the code

```php
strlen();
```

## PhanParamTooMany

This issue is emitted when you're passing more than the number of required and optional parameters than are defined for a method or function.

```
Call with {COUNT} arg(s) to {FUNCTIONLIKE} which only takes {COUNT} arg(s) defined at {FILE}:{LINE}
```

This will be emitted for the code

```php
function f7($i) {}
f7(1, 2);
```

## PhanParamTooManyCallable

```
Call with {COUNT} arg(s) to {FUNCTIONLIKE} (As a provided callable) which only takes {COUNT} arg(s) defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0365_array_map_callable.php.expected#L8) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0365_array_map_callable.php#L53).

## PhanParamTooManyInternal

This issue is emitted when you're passing more than the number of required and optional parameters than are defined for an internal method or function.

```
Call with {COUNT} arg(s) to {FUNCTIONLIKE} which only takes {COUNT} arg(s)
```

This will be emitted for the code

```php
strlen('str', 42);
```

## PhanParamTooManyUnpack

```
Call with {COUNT} or more args to {FUNCTIONLIKE} which only takes {COUNT} arg(s) defined at {FILE}:{LINE} (argument unpacking was used)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0562_unpack_too_many.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0562_unpack_too_many.php#L7).

## PhanParamTooManyUnpackInternal

```
Call with {COUNT} or more args to {FUNCTIONLIKE} which only takes {COUNT} arg(s) (argument unpacking was used)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0626_unpack_too_many_internal.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0626_unpack_too_many_internal.php#L5).

## PhanParamTypeMismatch

```
Argument {INDEX} is {TYPE} but {FUNCTIONLIKE} takes {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0364_extended_array_analyze.php.expected#L33) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0364_extended_array_analyze.php#L41).

# RedefineError

This category of issue comes up when more than one thing of whatever type have the same name and namespace.

## PhanIncompatibleCompositionMethod

```
Declaration of {METHOD} must be compatible with {METHOD} in {FILE} on line {LINE}
```

## PhanIncompatibleCompositionProp

```
{TRAIT} and {TRAIT} define the same property ({PROPERTY}) in the composition of {CLASS}. However, the definition differs and is considered incompatible. Class was composed in {FILE} on line {LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0207_incompatible_composition.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0207_incompatible_composition.php#L10).

## PhanRedefineClass


```
{CLASS} defined at {FILE}:{LINE} was previously defined as {CLASS} at {FILE}:{LINE}
```

This issue will be emitted for code like

```php
class C15 {}
class C15 {}
```

## PhanRedefineClassAlias

This issue is emitted when `class_alias` creates ambiguity in what the intended definition of a class is.
If possible, exclude one of the files containing the conflicting definitions.

```
{CLASS} aliased at {FILE}:{LINE} was previously defined as {CLASS} at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0278_class_alias.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0278_class_alias.php#L36).

## PhanRedefineClassConstant

```
Class constant {CONST} defined at {FILE}:{LINE} was previously defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0639_duplicate_const_prop.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0639_duplicate_const_prop.php#L9).

## PhanRedefineClassInternal


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


```php
function strlen() {}
```

## PhanRedefineProperty

```
Property ${PROPERTY} defined at {FILE}:{LINE} was previously defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0639_duplicate_const_prop.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0639_duplicate_const_prop.php#L5).

## PhanRedefinedClassReference

```
Saw reference to {CLASS} declared at {FILE}:{LINE} which is also declared at {FILE}:{LINE}. This may lead to confusing errors. It may be possible to exclude the class that isn't used with exclude_file_list. In addition to normal ways to suppress issues, this issue type can be suppressed on either of the class definitions if it is impractical to exclude one file.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0857_redefined_class_self.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0857_redefined_class_self.php#L5).

## PhanRedefinedExtendedClass

```
{CLASS} extends {CLASS} declared at {FILE}:{LINE} which is also declared at {FILE}:{LINE}. This may lead to confusing errors. It may be possible to exclude the class that isn't used with exclude_file_list.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0493_inherit_redefined.php.expected#L8) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0493_inherit_redefined.php#L12).

## PhanRedefinedInheritedInterface

```
{CLASS} inherits {INTERFACE} declared at {FILE}:{LINE} which is also declared at {FILE}:{LINE}. This may lead to confusing errors.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0493_inherit_redefined.php.expected#L9) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0493_inherit_redefined.php#L12).

## PhanRedefinedUsedTrait

```
{CLASS} uses {TRAIT} declared at {FILE}:{LINE} which is also declared at {FILE}:{LINE}. This may lead to confusing errors.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0493_inherit_redefined.php.expected#L10) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0493_inherit_redefined.php#L12).

# StaticCallError

## PhanAbstractStaticMethodCall

```
Potentially calling an abstract static method {METHOD} in {CODE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0867_abstract_call.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0867_abstract_call.php#L7).

## PhanAbstractStaticMethodCallInStatic

```
Potentially calling an abstract static method {METHOD} with static:: in {CODE} (the calling static method's class scope may be an abstract class)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0867_abstract_call.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0867_abstract_call.php#L8).

## PhanAbstractStaticMethodCallInTrait

```
Potentially calling an abstract static method {METHOD} on a trait in {CODE}, if the caller's method is called on the trait instead of a concrete class using the trait
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0867_abstract_call.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0867_abstract_call.php#L37).

## PhanStaticCallToNonStatic


```
Static call to non-static method {METHOD} defined at {FILE}:{LINE}
```


```php
class C19 { function f() {} }
C19::f();
```

## PhanStaticPropIsStaticType

```
Static property {PROPERTY} is declared to have type {TYPE}, but the only instance is shared among all subclasses (Did you mean {TYPE})
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0571_static_type_prop.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0571_static_type_prop.php#L14).

# TypeError

This category of issue come from using incorrect types or types that cannot cast to the expected types.

## PhanCoalescingAlwaysNull

```
Using {CODE} of type {TYPE} as the left hand side of a null coalescing (??) operation. The left hand side may be unnecessary.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0721_false_positive_coalesce.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0721_false_positive_coalesce.php#L7).

## PhanCoalescingAlwaysNullInGlobalScope

```
Using {CODE} of type {TYPE} as the left hand side of a null coalescing (??) operation. The left hand side may be unnecessary. (in the global scope - this is likely a false positive)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0708_loop_issue_examples.php.expected#L7) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0708_loop_issue_examples.php#L28).

## PhanCoalescingAlwaysNullInLoop

```
Using {CODE} of type {TYPE} as the left hand side of a null coalescing (??) operation. The left hand side may be unnecessary. (in a loop body - this is likely a false positive)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0708_loop_issue_examples.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0708_loop_issue_examples.php#L9).

## PhanCoalescingNeverNull

```
Using non-null {CODE} of type {TYPE} as the left hand side of a null coalescing (??) operation. The right hand side may be unnecessary.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0697_coalescing_always_never_null.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0697_coalescing_always_never_null.php#L4).

## PhanCoalescingNeverNullInGlobalScope

```
Using non-null {CODE} of type {TYPE} as the left hand side of a null coalescing (??) operation. The right hand side may be unnecessary. (in the global scope - this is likely a false positive)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0708_loop_issue_examples.php.expected#L9) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0708_loop_issue_examples.php#L30).

## PhanCoalescingNeverNullInLoop

```
Using non-null {CODE} of type {TYPE} as the left hand side of a null coalescing (??) operation. The right hand side may be unnecessary. (in a loop body - this is likely a false positive)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0708_loop_issue_examples.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0708_loop_issue_examples.php#L17).

## PhanDivisionByZero

```
Saw {CODE} with a divisor of type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0761_division_by_zero.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0761_division_by_zero.php#L5).

## PhanImpossibleCondition

```
Impossible attempt to cast {CODE} of type {TYPE} to {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0085_double_branch.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0085_double_branch.php#L4).

## PhanImpossibleConditionInGlobalScope

```
Impossible attempt to cast {CODE} of type {TYPE} to {TYPE} in the global scope (may be a false positive)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0211_assertion_cast.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0211_assertion_cast.php#L4).

## PhanImpossibleConditionInLoop

```
Impossible attempt to cast {CODE} of type {TYPE} to {TYPE} in a loop body (may be a false positive)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0708_loop_issue_examples.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0708_loop_issue_examples.php#L18).

## PhanImpossibleTypeComparison

```
Impossible attempt to check if {CODE} of type {TYPE} is identical to {CODE} of type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0807_int_cast.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0807_int_cast.php#L3).

## PhanImpossibleTypeComparisonInGlobalScope

```
Impossible attempt to check if {CODE} of type {TYPE} is identical to {CODE} of type {TYPE} in the global scope (likely a false positive)
```


## PhanImpossibleTypeComparisonInLoop

```
Impossible attempt to check if {CODE} of type {TYPE} is identical to {CODE} of type {TYPE} in a loop body (likely a false positive)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0865_array_key_int_or_string.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0865_array_key_int_or_string.php#L6).

## PhanInfiniteLoop

```
The loop condition {CODE} of type {TYPE} is always {TYPE} and nothing seems to exit the loop
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0803_noop_new.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0803_noop_new.php#L4).

## PhanInfiniteRecursion

NOTE: This is based on very simple heuristics. It has known false positives and false negatives.
This checks for a functionlike directly calling itself in a way that seems to be unconditionally (e.g. doesn't detect `a()` calling `b()` calling `a()`)

```
{FUNCTIONLIKE} is calling itself in a way that may cause infinite recursion.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/rasmus_files/expected/0007_self_call.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/rasmus_files/src/0007_self_call.php#L8).

## PhanInvalidMixin

```
Attempting to use a mixin of invalid or missing type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0806_mixin.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0806_mixin.php#L39).

## PhanMismatchVariadicComment

```
{PARAMETER} is variadic in comment, but not variadic in param ({PARAMETER})
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0258_variadic_comment_parsing.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0258_variadic_comment_parsing.php#L5).

## PhanMismatchVariadicParam

```
{PARAMETER} is not variadic in comment, but variadic in param ({PARAMETER})
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0258_variadic_comment_parsing.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0258_variadic_comment_parsing.php#L6).

## PhanModuloByZero

```
Saw {CODE} with modulus of type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0761_division_by_zero.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0761_division_by_zero.php#L6).

## PhanNonClassMethodCall


```
Call to method {METHOD} on non-class type {TYPE}
```

An example would come from

```php
$v8 = null;
$v8->f();
```

## PhanPartialTypeMismatchArgument

This issue (and similar issues) may be emitted when `strict_param_checking` is true, when analyzing a user-defined function.
(when some types of the argument's union type match, but not others.)

```
Argument {INDEX} (${PARAMETER}) is {CODE} of type {TYPE} but {FUNCTIONLIKE} takes {TYPE} ({TYPE} is incompatible) defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/088_possibly_invalid_argument.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/088_possibly_invalid_argument.php#L10).

## PhanPartialTypeMismatchArgumentInternal

This issue may be emitted when `strict_param_checking` is true, when analyzing an internal function.

```
Argument {INDEX} (${PARAMETER}) is {CODE} of type {TYPE} but {FUNCTIONLIKE} takes {TYPE} ({TYPE} is incompatible)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/025_strict_param_checks.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/025_strict_param_checks.php#L8).

## PhanPartialTypeMismatchProperty

This issue (and similar issues) may be emitted when `strict_property_checking` is true

```
Assigning {CODE} of type {TYPE} to property but {PROPERTY} is {TYPE} ({TYPE} is incompatible)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/024_strict_property_assignment.php.expected#L7) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/024_strict_property_assignment.php#L21).

## PhanPartialTypeMismatchReturn

This issue (and similar issues) may be emitted when `strict_return_checking` is true
(when some types of the return statement's union type match, but not others.)

```
Returning {CODE} of type {TYPE} but {FUNCTIONLIKE} is declared to return {TYPE} ({TYPE} is incompatible)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/159_array_unshift_convert_to_list.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/159_array_unshift_convert_to_list.php#L18).

## PhanPossiblyFalseTypeArgument

This issue may be emitted when `strict_param_checking` is true

```
Argument {INDEX} (${PARAMETER}) is {CODE} of type {TYPE} but {FUNCTIONLIKE} takes {TYPE} ({TYPE} is incompatible) defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/088_possibly_invalid_argument.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/088_possibly_invalid_argument.php#L6).

## PhanPossiblyFalseTypeArgumentInternal

This issue may be emitted when `strict_param_checking` is true

```
Argument {INDEX} (${PARAMETER}) is {CODE} of type {TYPE} but {FUNCTIONLIKE} takes {TYPE} ({TYPE} is incompatible)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/078_merge_bool_and.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/078_merge_bool_and.php#L3).

## PhanPossiblyFalseTypeMismatchProperty

```
Assigning {CODE} of type {TYPE} to property but {PROPERTY} is {TYPE} ({TYPE} is incompatible)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/024_strict_property_assignment.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/024_strict_property_assignment.php#L19).

## PhanPossiblyFalseTypeReturn

This issue may be emitted when `strict_return_checking` is true

```
Returning {CODE} of type {TYPE} but {FUNCTIONLIKE} is declared to return {TYPE} ({TYPE} is incompatible)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/026_strict_return_checks.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/026_strict_return_checks.php#L31).

## PhanPossiblyInfiniteRecursionSameParams

Note that when there are 1 or more parameters, this is only emitted when unused variable detection is enabled (needed to check for reassignments)

```
{FUNCTIONLIKE} is calling itself with the same parameters it was called with. This may cause infinite recursion (Phan does not check for changes to global or shared state).
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0802_min_max_explode_edge_case.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0802_min_max_explode_edge_case.php#L8).

## PhanPossiblyNonClassMethodCall

```
Call to method {METHOD} on type {TYPE} that could be a non-object
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/060_strict_method_check.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/060_strict_method_check.php#L12).

## PhanPossiblyNullTypeArgument

This issue may be emitted when `strict_param_checking` is true

```
Argument {INDEX} (${PARAMETER}) is {CODE} of type {TYPE} but {FUNCTIONLIKE} takes {TYPE} ({TYPE} is incompatible) defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/088_possibly_invalid_argument.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/088_possibly_invalid_argument.php#L8).

## PhanPossiblyNullTypeArgumentInternal

This issue may be emitted when `strict_param_checking` is true

```
Argument {INDEX} (${PARAMETER}) is {CODE} of type {TYPE} but {FUNCTIONLIKE} takes {TYPE} ({TYPE} is incompatible)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/025_strict_param_checks.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/025_strict_param_checks.php#L6).

## PhanPossiblyNullTypeMismatchProperty

```
Assigning {CODE} of type {TYPE} to property but {PROPERTY} is {TYPE} ({TYPE} is incompatible)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/024_strict_property_assignment.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/024_strict_property_assignment.php#L20).

## PhanPossiblyNullTypeReturn

This issue may be emitted when `strict_return_checking` is true

```
Returning {CODE} of type {TYPE} but {FUNCTIONLIKE} is declared to return {TYPE} ({TYPE} is incompatible)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/026_strict_return_checks.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/026_strict_return_checks.php#L16).

## PhanPowerOfZero

```
Saw {CODE} exponentiating to a power of type {TYPE} (the result will always be 1)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0761_division_by_zero.php.expected#L12) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0761_division_by_zero.php#L14).

## PhanRedundantCondition

```
Redundant attempt to cast {CODE} of type {TYPE} to {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0173_primitive_condition.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0173_primitive_condition.php#L3).

## PhanRedundantConditionInGlobalScope

```
Redundant attempt to cast {CODE} of type {TYPE} to {TYPE} in the global scope (likely a false positive)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0733_noop_in_for_loop.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0733_noop_in_for_loop.php#L7).

## PhanRedundantConditionInLoop

```
Redundant attempt to cast {CODE} of type {TYPE} to {TYPE} in a loop body (likely a false positive)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0698_loop_false_positive.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0698_loop_false_positive.php#L13).

## PhanRelativePathUsed


Relative paths are harder to reason about, and opcache may have issues with relative paths in edge cases.

```
{FUNCTION}() statement was passed a relative path {STRING_LITERAL} instead of an absolute path
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0545_require_testing.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0545_require_testing.php#L5).

## PhanSuspiciousLoopDirection

```
Suspicious loop appears to {DETAILS} after each iteration in {CODE}, but the loop condition is {CODE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0724_suspicious_comparison_in_loop.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0724_suspicious_comparison_in_loop.php#L8).

## PhanSuspiciousTruthyCondition

```
Suspicious attempt to check if {CODE} of type {TYPE} is truthy/falsey. This contains both objects/arrays and scalars
```

## PhanSuspiciousTruthyString

```
Suspicious attempt to check if {CODE} of type {TYPE} is truthy/falsey. This is false both for '' and '0'
```

## PhanSuspiciousValueComparison

```
Suspicious attempt to compare {CODE} of type {TYPE} to {CODE} of type {TYPE} with operator '{OPERATOR}'
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0644_unset_false_positive.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0644_unset_false_positive.php#L5).

## PhanSuspiciousValueComparisonInGlobalScope

```
Suspicious attempt to compare {CODE} of type {TYPE} to {CODE} of type {TYPE} with operator '{OPERATOR}' in the global scope (likely a false positive)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0526_crash.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0526_crash.php#L2).

## PhanSuspiciousValueComparisonInLoop

```
Suspicious attempt to compare {CODE} of type {TYPE} to {CODE} of type {TYPE} with operator '{OPERATOR}' in a loop (likely a false positive)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0767_literal_count.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0767_literal_count.php#L16).

## PhanSuspiciousWeakTypeComparison

Some of these issues may be valid, but these are often confusing ways of checking for empty/null/false.
Phan does not support all types of comparisons (e.g. extensions may define comparisons on data types)

```
Suspicious attempt to compare {CODE} of type {TYPE} to {CODE} of type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0731_weak_equality.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0731_weak_equality.php#L6).

## PhanSuspiciousWeakTypeComparisonInGlobalScope

```
Suspicious attempt to compare {CODE} of type {TYPE} to {CODE} of type {TYPE} in the global scope (likely a false positive)
```

## PhanSuspiciousWeakTypeComparisonInLoop

```
Suspicious attempt to compare {CODE} of type {TYPE} to {CODE} of type {TYPE} in a loop body (likely a false positive)
```

## PhanTypeArrayOperator

```
Invalid array operand provided to operator '{OPERATOR}' between types {TYPE} and {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0574_array_op.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0574_array_op.php#L9).

## PhanTypeArraySuspicious


```
Suspicious array access to {CODE} of type {TYPE}
```

This issue will be emitted for the following code

```php
$a = false; if($a[1]) {}
```

## PhanTypeArraySuspiciousNull

```
Suspicious array access to {CODE} of type null
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0739_access_null.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0739_access_null.php#L5).

## PhanTypeArraySuspiciousNullable

```
Suspicious array access to {CODE} of nullable type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0287_suspicious_nullable_array.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0287_suspicious_nullable_array.php#L3).

## PhanTypeArrayUnsetSuspicious

```
Suspicious attempt to unset an offset of a value {CODE} of type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0442_unset_suspicious.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0442_unset_suspicious.php#L4).

## PhanTypeComparisonFromArray


```
array to {TYPE} comparison
```

An example would be

```php
if ([1, 2] == 'string') {}
```

## PhanTypeComparisonToArray


```
{TYPE} to array comparison
```

An example would be

```php
if (42 == [1, 2]) {}
```

## PhanTypeComparisonToInvalidClass

```
Saw code asserting that an expression has a class, but that class is an invalid/impossible FQSEN {STRING_LITERAL}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0305_is_a_tests.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0305_is_a_tests.php#L10).

## PhanTypeComparisonToInvalidClassType

```
Saw code asserting that an expression has a class, but saw an invalid/impossible union type {TYPE} (expected {TYPE})
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0570_switch_on_class.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0570_switch_on_class.php#L6).

## PhanTypeConversionFromArray

```
array to {TYPE} conversion
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0532_empty_array_element.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0532_empty_array_element.php#L2).

## PhanTypeErrorInInternalCall

NOTE: This is only emitted for the functions that `enable_extended_internal_return_type_plugins` would try to infer literal return values of.

```
Saw a call to an internal function {FUNCTION}() with what would be invalid arguments in strict mode, when trying to infer the return value literal type: {DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/101_extended_return_inferences.php.expected#L12) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/101_extended_return_inferences.php#L22).

## PhanTypeErrorInOperation

```
Saw an error when attempting to infer the type of expression {CODE}: {DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0678_invalid_operations.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0678_invalid_operations.php#L8).

## PhanTypeExpectedObject

```
Expected an object instance but saw expression {CODE} with type {TYPE}
```

## PhanTypeExpectedObjectOrClassName

```
Expected an object instance or the name of a class but saw expression {CODE} with type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0590_crash_test.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0590_crash_test.php#L7).

## PhanTypeExpectedObjectPropAccess

```
Expected an object instance when accessing an instance property, but saw an expression {CODE} with type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0541_unset.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0541_unset.php#L11).

## PhanTypeExpectedObjectPropAccessButGotNull

```
Expected an object instance when accessing an instance property, but saw an expression {CODE} with type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0379_bad_prop_access.php.expected#L7) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0379_bad_prop_access.php#L28).

## PhanTypeExpectedObjectStaticPropAccess

```
Expected an object instance or a class name when accessing a static property, but saw an expression {CODE} with type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0379_bad_prop_access.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0379_bad_prop_access.php#L9).

## PhanTypeInstantiateAbstract

```
Instantiation of abstract class {CLASS}
```

This issue will be emitted for the following code

```php
abstract class D {} (new D);
```

## PhanTypeInstantiateAbstractStatic

```
Potential instantiation of abstract class {CLASS} (not an issue if this method is only called from a non-abstract subclass)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0679_static_from_type.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0679_static_from_type.php#L4).

## PhanTypeInstantiateInterface

```
Instantiation of interface {INTERFACE}
```

This issue will be emitted for the following code

```php
interface E {} (new E);
```

## PhanTypeInstantiateTrait

```
Instantiation of trait {TRAIT}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0624_instantiate_abstract.php.expected#L11) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0624_instantiate_abstract.php#L42).

## PhanTypeInstantiateTraitStaticOrSelf

```
Potential instantiation of trait {TRAIT} (not an issue if this method is only called from a non-abstract class using the trait)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0624_instantiate_abstract.php.expected#L12) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0624_instantiate_abstract.php#L43).

## PhanTypeInvalidBitwiseBinaryOperator

```
Invalid non-int/non-string operand provided to operator '{OPERATOR}' between types {TYPE} and {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/138_suspicious_object_comparison.php.expected#L10) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/138_suspicious_object_comparison.php#L15).

## PhanTypeInvalidCallExpressionAssignment

```
Probably unused assignment to function result {CODE} for function returning {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0232_assignment_to_call.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0232_assignment_to_call.php#L7).

## PhanTypeInvalidCallable

```
Saw type {TYPE} which cannot be a callable
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/051_invalid_function_node.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/051_invalid_function_node.php#L2).

## PhanTypeInvalidCallableArrayKey

```
In a place where phan was expecting a callable, saw an array with an unexpected key for element #{INDEX} (expected [$class_or_expr, $method_name])
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/089_invalid_callable_key.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/089_invalid_callable_key.php#L3).

## PhanTypeInvalidCallableArraySize

```
In a place where phan was expecting a callable, saw an array of size {COUNT}, but callable arrays must be of size 2
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/062_strict_function_checking.php.expected#L10) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/062_strict_function_checking.php#L42).

## PhanTypeInvalidCallableMethodName

```
Method name of callable must be a string, got {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0540_invalid_method_name.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0540_invalid_method_name.php#L9).

## PhanTypeInvalidCallableObjectOfMethod

```
In a place where phan was expecting a callable, saw a two-element array with a class or expression with an unexpected type {TYPE} (expected a class type or string). Method name was {METHOD}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0521_misuse_closure_type.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0521_misuse_closure_type.php#L18).

## PhanTypeInvalidCloneNotObject

```
Expected an object to be passed to clone() but got {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0549_invalid_clone.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0549_invalid_clone.php#L2).

## PhanTypeInvalidClosureScope

```
Invalid @phan-closure-scope: expected a class name, got {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0537_closure_scope.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0537_closure_scope.php#L8).

## PhanTypeInvalidDimOffset

```
Invalid offset {SCALAR} of {CODE} of array type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0439_multi.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0439_multi.php#L4).

## PhanTypeInvalidDimOffsetArrayDestructuring

```
Invalid offset {SCALAR} of {CODE} of array type {TYPE} in an array destructuring assignment
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0402_array_destructuring.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0402_array_destructuring.php#L4).

## PhanTypeInvalidEval

```
Eval statement was passed an invalid expression of type {TYPE} (expected a string)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0546_require_other_testing.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0546_require_other_testing.php#L7).

## PhanTypeInvalidExpressionArrayDestructuring

```
Invalid value {CODE} of type {TYPE} in an array destructuring assignment, expected {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/rasmus_files/expected/0021_foreach.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/rasmus_files/src/0021_foreach.php#L7).

## PhanTypeInvalidInstanceof

```
Found an instanceof class name {CODE} of type {TYPE}, but class name must be a valid object or a string
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0346_dynamic_instanceof.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0346_dynamic_instanceof.php#L24).

## PhanTypeInvalidLeftOperand

```
Invalid operator: right operand is array and left is not
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0114_array_concatenation.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0114_array_concatenation.php#L12).

## PhanTypeInvalidLeftOperandOfAdd

```
Invalid operator: left operand of {OPERATOR} is {TYPE} (expected array or number)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0580_numeric_string.php.expected#L14) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0580_numeric_string.php#L23).

## PhanTypeInvalidLeftOperandOfIntegerOp

```
Invalid operator: left operand of {OPERATOR} is {TYPE} (expected int)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0628_arithmetic_op_more_warn.php.expected#L13) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0628_arithmetic_op_more_warn.php#L27).

## PhanTypeInvalidLeftOperandOfNumericOp

```
Invalid operator: left operand of {OPERATOR} is {TYPE} (expected number)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0148_invalid_array.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0148_invalid_array.php#L15).

## PhanTypeInvalidMethodName

```
Instance method name must be a string, got {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0540_invalid_method_name.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0540_invalid_method_name.php#L4).

## PhanTypeInvalidPropertyName

```
Saw a dynamic usage of an instance property with a name of type {TYPE} but expected the name to be a string
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0636_invalid_property_name_type.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0636_invalid_property_name_type.php#L8).

## PhanTypeInvalidRequire

```
Require statement was passed an invalid expression of type {TYPE} (expected a string)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0545_require_testing.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0545_require_testing.php#L2).

## PhanTypeInvalidRightOperand

```
Invalid operator: left operand is array and right is not
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/004_partial_arithmetic.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/004_partial_arithmetic.php#L7).

## PhanTypeInvalidRightOperandOfAdd

```
Invalid operator: right operand of {OPERATOR} is {TYPE} (expected array or number)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0012_closures.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0012_closures.php#L15).

## PhanTypeInvalidRightOperandOfIntegerOp

```
Invalid operator: right operand of {OPERATOR} is {TYPE} (expected int)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0628_arithmetic_op_more_warn.php.expected#L9) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0628_arithmetic_op_more_warn.php#L16).

## PhanTypeInvalidRightOperandOfNumericOp

```
Invalid operator: right operand of {OPERATOR} is {TYPE} (expected number)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0574_array_op.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0574_array_op.php#L8).

## PhanTypeInvalidStaticMethodName

```
Static method name must be a string, got {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0540_invalid_method_name.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0540_invalid_method_name.php#L6).

## PhanTypeInvalidStaticPropertyName

```
Saw a dynamic usage of a static property with a name of type {TYPE} but expected the name to be a string
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0636_invalid_property_name_type.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0636_invalid_property_name_type.php#L5).

## PhanTypeInvalidThrowStatementNonThrowable

```
{FUNCTIONLIKE} can throw {CODE} of type {TYPE} here which can't cast to {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/085_throw_type_mismatch.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/085_throw_type_mismatch.php#L19).

## PhanTypeInvalidThrowsIsInterface

```
@throws annotation of {FUNCTIONLIKE} has suspicious interface type {TYPE} for an @throws annotation, expected class (PHP allows interfaces to be caught, so this might be intentional)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0454_throws.php.expected#L10) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0454_throws.php#L57).

## PhanTypeInvalidThrowsIsTrait

```
@throws annotation of {FUNCTIONLIKE} has invalid trait type {TYPE}, expected a class
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0454_throws.php.expected#L11) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0454_throws.php#L60).

## PhanTypeInvalidThrowsNonObject

```
@throws annotation of {FUNCTIONLIKE} has invalid non-object type {TYPE}, expected a class
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0454_throws.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0454_throws.php#L33).

## PhanTypeInvalidThrowsNonThrowable

```
@throws annotation of {FUNCTIONLIKE} has suspicious class type {TYPE}, which does not extend Error/Exception
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0454_throws.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0454_throws.php#L30).

## PhanTypeInvalidTraitParam

```
{FUNCTIONLIKE} is declared to have a parameter ${PARAMETER} with a real type of trait {TYPE} (expected a class or interface or built-in type)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0560_trait_in_param_return.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0560_trait_in_param_return.php#L8).

## PhanTypeInvalidTraitReturn

```
Expected a class or interface (or built-in type) to be the real return type of {FUNCTIONLIKE} but got trait {TRAIT}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0560_trait_in_param_return.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0560_trait_in_param_return.php#L8).

## PhanTypeInvalidUnaryOperandBitwiseNot

```
Invalid operator: unary operand of {STRING_LITERAL} is {TYPE} (expected number or string)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0507_unary_op_warn.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0507_unary_op_warn.php#L2).

## PhanTypeInvalidUnaryOperandIncOrDec

```
Invalid operator: unary operand of {STRING_LITERAL} is {TYPE} (expected int or string or float)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0645_increment_branch.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0645_increment_branch.php#L5).

## PhanTypeInvalidUnaryOperandNumeric

```
Invalid operator: unary operand of {STRING_LITERAL} is {TYPE} (expected number)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0507_unary_op_warn.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0507_unary_op_warn.php#L8).

## PhanTypeInvalidYieldFrom

```
Yield from statement was passed an invalid expression {CODE} of type {TYPE} (expected Traversable/array)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0774_empty_foreach.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0774_empty_foreach.php#L15).

## PhanTypeMagicVoidWithReturn

```
Found a return statement with a value in the implementation of the magic method {METHOD}, expected void return type
```

This will be emitted for the code

```php
// (PHP ignores the return value of some magic methods, such as __set)
class A { public function __set() { return true; } }
```

## PhanTypeMismatchArgument

```
Argument {INDEX} (${PARAMETER}) is {CODE} of type {TYPE} but {FUNCTIONLIKE} takes {TYPE} defined at {FILE}:{LINE}
```

This will be emitted for the code

```php
function f8(int $i) {}
f8('string');
```

## PhanTypeMismatchArgumentInternal

```
Argument {INDEX} (${PARAMETER}) is {CODE} of type {TYPE} but {FUNCTIONLIKE} takes {TYPE}
```

This will be emitted for the code

```php
strlen(42);
```

## PhanTypeMismatchArgumentInternalProbablyReal

```
Argument {INDEX} (${PARAMETER}) is {CODE} of type {TYPE}{DETAILS} but {FUNCTIONLIKE} takes {TYPE}{DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/021_binary_op.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/021_binary_op.php#L2).

## PhanTypeMismatchArgumentInternalReal

Due to lack of reflection information, this will rarely ever be emitted when phan is run with php 7.3 or older.
PHP 7.4 and 8.0 are expected to add more reflection type information for parameters of internal functions/methods.

```
Argument {INDEX} (${PARAMETER}) is {CODE} of type {TYPE}{DETAILS} but {FUNCTIONLIKE} takes {TYPE}{DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/170_stub_file.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/170_stub_file.php#L2).

## PhanTypeMismatchArgumentNullable

```
Argument {INDEX} (${PARAMETER}) is {CODE} of type {TYPE} but {FUNCTIONLIKE} takes {TYPE} defined at {FILE}:{LINE} (expected type to be non-nullable)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0086_conditional_instanceof_type.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0086_conditional_instanceof_type.php#L14).

## PhanTypeMismatchArgumentNullableInternal

```
Argument {INDEX} (${PARAMETER}) is {CODE} of type {TYPE} but {FUNCTIONLIKE} takes {TYPE} (expected type to be non-nullable)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0152_closure_casts_callable.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0152_closure_casts_callable.php#L4).

## PhanTypeMismatchArgumentPropertyReference

```
Argument {INDEX} is property {PROPERTY} with type {TYPE} but {FUNCTIONLIKE} takes a reference of type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0748_property_incompatible_reference.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0748_property_incompatible_reference.php#L31).

## PhanTypeMismatchArgumentPropertyReferenceReal

```
Argument {INDEX} is property {PROPERTY} with type {TYPE}{DETAILS} but {FUNCTIONLIKE} takes a reference of type {TYPE}{DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0752_local_property_reference.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0752_local_property_reference.php#L13).

## PhanTypeMismatchArgumentReal

This is a more severe version of `PhanTypeMismatchArgument` for code that Phan infers is likely to throw an Error at runtime.
This ignores some configuration settings allowing nulls to cast to other types, etc.
It is emitted instead of `PhanTypeMismatchArgument` under the following conditions:

- Phan infers real types for both the argument expression and the parameter's real signature.
- The union type of the argument doesn't have any types that are partially compatible with the return type from the signature.
- If `strict_types` isn't enabled in the caller, it won't be emitted if the returned expression could be a non-null scalar and the declared return type has any scalars.

This does not attempt to account for the possibility of overriding methods being more permissive about what argument types are accepted.

```
Argument {INDEX} (${PARAMETER}) is {CODE} of type {TYPE}{DETAILS} but {FUNCTIONLIKE} takes {TYPE}{DETAILS} defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/rasmus_files/expected/0035_class_const.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/rasmus_files/src/0035_class_const.php#L6).

## PhanTypeMismatchArrayDestructuringKey

```
Attempting an array destructing assignment with a key of type {TYPE} but the only key types of the right-hand side are of type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0402_array_destructuring.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0402_array_destructuring.php#L4).

## PhanTypeMismatchBitwiseBinaryOperands

```
Unexpected mix of int and string operands provided to operator '{OPERATOR}' between types {TYPE} and {TYPE} (expected one type but not both)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0561_bitwise_operands.php.expected#L8) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0561_bitwise_operands.php#L11).

## PhanTypeMismatchDeclaredParam

```
Doc-block of ${PARAMETER} in {METHOD} contains phpdoc param type {TYPE} which is incompatible with the param type {TYPE} declared in the signature
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0863_vaguer_type.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0863_vaguer_type.php#L3).

## PhanTypeMismatchDeclaredParamNullable

```
Doc-block of ${PARAMETER} in {METHOD} is phpdoc param type {TYPE} which is not a permitted replacement of the nullable param type {TYPE} declared in the signature ('?T' should be documented as 'T|null' or '?T')
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0005_compat.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0005_compat.php#L21).

## PhanTypeMismatchDeclaredReturn

```
Doc-block of {METHOD} contains declared return type {TYPE} which is incompatible with the return type {TYPE} declared in the signature
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0486_crash_test.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0486_crash_test.php#L7).

## PhanTypeMismatchDeclaredReturnNullable

```
Doc-block of {METHOD} has declared return type {TYPE} which is not a permitted replacement of the nullable return type {TYPE} declared in the signature ('?T' should be documented as 'T|null' or '?T')
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0253_return_type_match.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0253_return_type_match.php#L46).

## PhanTypeMismatchDefault

```
Default value for {TYPE} ${PARAMETER} can't be {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/rasmus_files/expected/0030_def_arg_type.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/rasmus_files/src/0030_def_arg_type.php#L4).

## PhanTypeMismatchDimAssignment

```
When appending to a value of type {TYPE}, found an array access index of type {TYPE}, but expected the index to be of type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0602_array_key_from_template.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0602_array_key_from_template.php#L14).

## PhanTypeMismatchDimEmpty

```
Assigning to an empty array index of a value of type {TYPE}, but expected the index to exist and be of type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0354_string_index.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0354_string_index.php#L10).

## PhanTypeMismatchDimFetch

```
When fetching an array index from a value of type {TYPE}, found an array index of type {TYPE}, but expected the index to be of type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0399_array_key.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0399_array_key.php#L8).

## PhanTypeMismatchDimFetchNullable

```
When fetching an array index from a value of type {TYPE}, found an array index of type {TYPE}, but expected the index to be of the non-nullable type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0429_nullable_offsets.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0429_nullable_offsets.php#L16).

## PhanTypeMismatchForeach

This issue will be emitted when something that can't be an array is passed as the array_expression.

```
{TYPE} passed to foreach instead of array
```

This will be emitted for the code

```php
foreach (null as $i) {}
```

## PhanTypeMismatchGeneratorYieldKey

```
Yield statement has a key {CODE} with type {TYPE} but {FUNCTIONLIKE} is declared to yield keys of type {TYPE} in {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0475_analyze_yield_from.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0475_analyze_yield_from.php#L24).

## PhanTypeMismatchGeneratorYieldValue

```
Yield statement has a value {CODE} with type {TYPE} but {FUNCTIONLIKE} is declared to yield values of type {TYPE} in {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0475_analyze_yield_from.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0475_analyze_yield_from.php#L23).

## PhanTypeMismatchProperty

```
Assigning {CODE} of type {TYPE} to property but {PROPERTY} is {TYPE}
```

This issue is emitted from the following code

```php
function f(int $p = false) {}
```

## PhanTypeMismatchPropertyByRef

```
{CODE} of type {TYPE} may end up assigned to property {PROPERTY} of type {TYPE} by reference at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0748_property_incompatible_reference.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0748_property_incompatible_reference.php#L25).

## PhanTypeMismatchPropertyDefault

```
Default value for {TYPE} ${PROPERTY} can't be {CODE} of type {TYPE} based on phpdoc types
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0006_property_types.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0006_property_types.php#L6).

## PhanTypeMismatchPropertyDefaultReal

```
Default value for {TYPE} ${PROPERTY} can't be {CODE} of type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/expected/014_real_type_mismatch.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/src/014_real_type_mismatch.php#L6).

## PhanTypeMismatchPropertyReal

```
Assigning {CODE} of type {TYPE} to property but {PROPERTY} is {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/expected/012_typed_properties_errors.php.expected#L11) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php70_files/src/012_typed_properties_errors.php#L20).

## PhanTypeMismatchPropertyRealByRef

```
{CODE} of type {TYPE} may end up assigned to property {PROPERTY} of type {TYPE} by reference at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/expected/016_typed_property_by_reference.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/src/016_typed_property_by_reference.php#L25).

## PhanTypeMismatchReturn

```
Returning {CODE} of type {TYPE} but {FUNCTIONLIKE} is declared to return {TYPE}
```

This issue is emitted from the following code

```php
class G { /** @param string $s */ function f($s) : int { return $s; } }
```

## PhanTypeMismatchReturnNullable

```
Returning {CODE} of type {TYPE} but {FUNCTIONLIKE} is declared to return {TYPE} (expected returned value to be non-nullable)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0656_nullable_return.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0656_nullable_return.php#L4).

## PhanTypeMismatchReturnReal

This is a more severe version of `PhanTypeMismatchReturn` for code that Phan infers is likely to throw an Error at runtime.
This ignores some configuration settings allowing nulls to cast to other types, etc.
It is emitted instead of `PhanTypeMismatchReturn` under the following conditions:

- Phan infers real types for both the returned expression and the function's signature
- The union type of the returned expression doesn't have any types that are partially compatible with the return type from the signature.
- If `strict_types` isn't enabled, it won't be emitted if the returned expression could be a non-null scalar and the declared return type has any scalars.

```
Returning {CODE} of type {TYPE}{DETAILS} but {FUNCTIONLIKE} is declared to return {TYPE}{DETAILS}
```

This issue is emitted from the following code

```php
class G { function f() : int { return 'string'; } }
```


## PhanTypeMismatchUnpackKey

```
When unpacking a value of type {TYPE}, the value's keys were of type {TYPE}, but the keys should be consecutive integers starting from 0
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0809_shuffle_converts_to_list.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0809_shuffle_converts_to_list.php#L18).

## PhanTypeMismatchUnpackKeyArraySpread

```
When unpacking a value of type {TYPE}, the value's keys were of type {TYPE}, but the keys should be integers
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/expected/021_associative_array_casting_rules.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/src/021_associative_array_casting_rules.php#L11).

## PhanTypeMismatchUnpackValue

```
Attempting to unpack a value of type {TYPE} which does not contain any subtypes of iterable (such as array or Traversable)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/expected/019_unpack_allowed_in_const_expr.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/src/019_unpack_allowed_in_const_expr.php#L6).

## PhanTypeMissingReturn

```
Method {METHOD} is declared to return {TYPE} in phpdoc but has no return value
```

This issue is emitted from the following code

```php
class H { function f() : int {} }
```

## PhanTypeMissingReturnReal

```
Method {METHOD} is declared to return {TYPE} in its real type signature but has no return value
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0242_void_71.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0242_void_71.php#L4).

## PhanTypeNoAccessiblePropertiesForeach

```
Class {TYPE} was passed to foreach, but it does not extend Traversable and none of its declared properties are accessible from this context. (This check excludes dynamic properties)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0542_foreach_non_traversable.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0542_foreach_non_traversable.php#L41).

## PhanTypeNoPropertiesForeach

Note: This and other checks of `foreach` deliberately don't warn about `stdClass` for now.

```
Class {TYPE} was passed to foreach, but it does not extend Traversable and doesn't have any declared properties. (This check excludes dynamic properties)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0246_iterable.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0246_iterable.php#L9).

## PhanTypeNonVarPassByRef

```
Only variables can be passed by reference at argument {INDEX} of {FUNCTIONLIKE}
```

This issue is emitted from the following code

```php
class F { static function f(&$v) {} } F::f('string');
```

## PhanTypeNonVarReturnByRef

```
Only variables can be returned by reference in {FUNCTIONLIKE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/expected/017_arrow_func_use_retval.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/php74_files/src/017_arrow_func_use_retval.php#L13).

## PhanTypeObjectUnsetDeclaredProperty

```
Suspicious attempt to unset class {TYPE}'s property {PROPERTY} declared at {FILE}:{LINE} (This can be done, but is more commonly done for dynamic properties and Phan does not expect this)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0541_unset.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0541_unset.php#L7).

## PhanTypeParentConstructorCalled

```
Must call parent::__construct() from {CLASS} which extends {CLASS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0283_parent_constructor_called.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0283_parent_constructor_called.php#L6).

## PhanTypePossiblyInvalidCallable

```
Saw type {TYPE} which is possibly not a callable
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/062_strict_function_checking.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/062_strict_function_checking.php#L33).

## PhanTypePossiblyInvalidCloneNotObject

```
Expected an object to be passed to clone() but got possible non-object {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/144_bad_clone.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/144_bad_clone.php#L14).

## PhanTypePossiblyInvalidDimOffset

```
Possibly invalid offset {SCALAR} of {CODE} of array type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0831_possibly_undefined.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0831_possibly_undefined.php#L8).

## PhanTypeSuspiciousEcho

```
Suspicious argument {CODE} of type {TYPE} for an echo/print statement
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0028_if_condition_assignment.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0028_if_condition_assignment.php#L3).

## PhanTypeSuspiciousIndirectVariable

```
Indirect variable ${(expr)} has invalid inner expression type {TYPE}, expected string/integer
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0298_weird_variable_name.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0298_weird_variable_name.php#L10).

## PhanTypeSuspiciousNonTraversableForeach

```
Class {TYPE} was passed to foreach, but it does not extend Traversable. This may be intentional, because some of that class's declared properties are accessible from this context. (This check excludes dynamic properties)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0542_foreach_non_traversable.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0542_foreach_non_traversable.php#L22).

## PhanTypeSuspiciousStringExpression

```
Suspicious type {TYPE} of a variable or expression {CODE} used to build a string. (Expected type to be able to cast to a string)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0581_concat_non_string.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0581_concat_non_string.php#L5).

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

This category of issue comes up when there are references to undefined things. These are a big source of false-positives in Phan given that code bases often take liberties with calling methods on sub-classes of the class defined to be returned by a function and things like that.

You can ignore all errors of this category by passing in the command-line argument `-i` or `--ignore-undeclared`.

## PhanAmbiguousTraitAliasSource

```
Trait alias {METHOD} has an ambiguous source method {METHOD} with more than one possible source trait. Possibilities: {TRAIT}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0297_ambiguous_trait_source.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0297_ambiguous_trait_source.php#L7).

## PhanClassContainsAbstractMethod

```
non-abstract class {CLASS} contains abstract method {METHOD} declared at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0493_inherit_redefined.php.expected#L7) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0493_inherit_redefined.php#L12).

## PhanClassContainsAbstractMethodInternal

```
non-abstract class {CLASS} contains abstract internal method {METHOD}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0188_prop_array_access.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0188_prop_array_access.php#L2).

## PhanEmptyFQSENInCallable

```
Possible call to a function '{FUNCTIONLIKE}' with an empty FQSEN.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/063_invalid_fqsen.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/063_invalid_fqsen.php#L2).

## PhanEmptyFQSENInClasslike

```
Possible use of a classlike '{CLASSLIKE}' with an empty FQSEN.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/053_empty_fqsen.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/053_empty_fqsen.php#L3).

## PhanEmptyFile

This low severity issue is emitted for empty files.

```
Empty file {FILE}
```

This would be emitted if you have a file with the contents

```php
```

## PhanInvalidFQSENInCallable

```
Possible call to a function '{FUNCTIONLIKE}' with an invalid FQSEN.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/063_invalid_fqsen.php.expected#L14) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/063_invalid_fqsen.php#L15).

## PhanInvalidFQSENInClasslike

```
Possible use of a classlike '{CLASSLIKE}' with an invalid FQSEN.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/027_invalid_new.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/027_invalid_new.php#L2).

## PhanInvalidRequireFile

```
Required file {FILE} is not a file
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0545_require_testing.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0545_require_testing.php#L5).

## PhanMissingRequireFile

This is emitted when a statement such as `require` or `include_once` refers to a path that doesn't exist.

If this is warning about a relative include, then you may want to adjust the config settings for `include_paths` and optionally `warn_about_relative_include_paths`.


```
Missing required file {FILE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0545_require_testing.php.expected#L11) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0545_require_testing.php#L10).

## PhanParentlessClass


```
Reference to parent of class {CLASS} that does not extend anything
```

This issue will be emitted from the following code

```php
class F { function f() { $v = parent::f(); } }
```

## PhanPossiblyUndeclaredGlobalVariable

```
Global variable ${VARIABLE} is possibly undeclared
```


This may have a lot of false positives, and you may wish to suppress this issue entirely, either through `suppress_issue_list` or with the config setting `ignore_undeclared_variables_in_global_scope`

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0158_conditional_assignment.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0158_conditional_assignment.php#L7).

## PhanPossiblyUndeclaredMethod

```
Call to possibly undeclared method {METHOD} on type {TYPE} ({TYPE} does not declare the method)
```

## PhanPossiblyUndeclaredProperty

```
Reference to possibly undeclared property {PROPERTY} of expression of type {TYPE} ({TYPE} does not declare that property)
```

## PhanPossiblyUndeclaredVariable

```
Variable ${VARIABLE} is possibly undeclared
```


This may have a lot of false positives, and you may wish to suppress this issue entirely in the config setting `suppress_issue_list`.
Phan does not attempt to analyze the relationship between variables or conditions at all, e.g. `PhanPossiblyUndeclaredVariable` will be emitted for the below snippet:

```php
if ($cond) { $var = expr; }
// ...
if ($cond) { use($var); }
```

## PhanPossiblyUnsetPropertyOfThis

```
Attempting to read property {PROPERTY} which was unset in the current scope
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0742_dynamic_property_tracking.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0742_dynamic_property_tracking.php#L8).

## PhanRequiredTraitNotAdded

This happens when a trait name is used in a trait adaptations clause, but that trait wasn't added to the class.

```
Required trait {TRAIT} for trait adaptation was not added to class
```


```php
trait T1 {}
trait T2 {}
class A {
	use T1 {T2::foo as bar;}
}
```

## PhanTraitParentReference


```
Reference to parent from trait {TRAIT}
```

This issue will be emitted from the following code

```php
trait T { function f() { return parent::f(); } }
```

## PhanUndeclaredAliasedMethodOfTrait

```
Alias {METHOD} was defined for a method {METHOD} which does not exist in trait {TRAIT}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/013_traits12.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/013_traits12.php#L3).

## PhanUndeclaredClass

```
Reference to undeclared class {CLASS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0504_prop_assignment_fetch.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0504_prop_assignment_fetch.php#L16).

## PhanUndeclaredClassAliasOriginal

```
Reference to undeclared class {CLASS} for the original class of a class_alias for {CLASS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0278_class_alias.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0278_class_alias.php#L34).

## PhanUndeclaredClassCatch


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

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0049_undefined_constant.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0049_undefined_constant.php#L2).

## PhanUndeclaredClassInCallable

```
Reference to undeclared class {CLASS} in callable {METHOD}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/094_shutdown_function.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/094_shutdown_function.php#L4).

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

## PhanUndeclaredClassProperty

```
Reference to instance property {PROPERTY} from undeclared class {CLASS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0080_undefined_class.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0080_undefined_class.php#L4).

## PhanUndeclaredClassReference

```
Reference to undeclared class {CLASS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0145_class_implicit_constant.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0145_class_implicit_constant.php#L7).

## PhanUndeclaredClassStaticProperty

```
Reference to static property {PROPERTY} from undeclared class {CLASS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0534_missing_static_property.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0534_missing_static_property.php#L8).

## PhanUndeclaredClosureScope

```
Reference to undeclared class {CLASS} in @phan-closure-scope
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/068_template_typeof.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/068_template_typeof.php#L8).

## PhanUndeclaredConstant

This issue comes up when you reference a constant that doesn't exist.

```
Reference to undeclared constant {CONST}
```


```php
$v7 = UNDECLARED_CONSTANT;
```
## PhanUndeclaredConstantOfClass

```
Reference to undeclared class constant {CONST}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/015_class_const_declaration9.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/015_class_const_declaration9.php#L6).

## PhanUndeclaredExtendedClass


```
Class extends undeclared class {CLASS}
```

This issue will be emitted from the following code

```php
class E extends UndeclaredClass {}
```

## PhanUndeclaredFunction

This issue will be emitted if you reference a function that doesn't exist.

```
Call to undeclared function {FUNCTION}
```

This issue will be emitted for the code

```php
some_missing_function();
```

## PhanUndeclaredFunctionInCallable

```
Call to undeclared function {FUNCTION} in callable
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/094_shutdown_function.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/094_shutdown_function.php#L3).

## PhanUndeclaredGlobalVariable

```
Global variable ${VARIABLE} is undeclared
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/rewriting_test/expected/004_crash_rewrite_if.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/rewriting_test/src/004_crash_rewrite_if.php#L2).

## PhanUndeclaredInterface


```
Class implements undeclared interface {INTERFACE}
```


```php
class C17 implements UndeclaredInterface {}
```

## PhanUndeclaredInvokeInCallable

```
Possible attempt to access missing magic method {FUNCTIONLIKE} of '{CLASS}'
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0806_mixin.php.expected#L12) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0806_mixin.php#L54).

## PhanUndeclaredMagicConstant

```
Reference to magic constant {CONST} that is undeclared in the current scope: {DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0594_magic_constant.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0594_magic_constant.php#L2).

## PhanUndeclaredMethod

```
Call to undeclared method {METHOD}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0819_stdclass_descendant_class.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0819_stdclass_descendant_class.php#L18).

## PhanUndeclaredMethodInCallable

```
Call to undeclared method {METHOD} in callable. Possible object type(s) for that method are {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0521_misuse_closure_type.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0521_misuse_closure_type.php#L16).

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

## PhanUndeclaredStaticMethodInCallable

```
Reference to undeclared static method {METHOD} in callable
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/094_shutdown_function.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/094_shutdown_function.php#L6).

## PhanUndeclaredStaticProperty


```
Static property '{PROPERTY}' on {CLASS} is undeclared
```

An example would be

```php
class C22 {}
$v11 = C22::$p;
```

## PhanUndeclaredThis

```
Variable ${VARIABLE} is undeclared
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0140_class_context.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0140_class_context.php#L7).

## PhanUndeclaredTrait


```
Class uses undeclared trait {TRAIT}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0048_parent_class_exists.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0048_parent_class_exists.php#L10).

## PhanUndeclaredTypeParameter


```
Parameter ${PARAMETER} has undeclared type {TYPE}
```

This issue will be emitted from the following code

```php
function f(Undef $p) {}
```

## PhanUndeclaredTypeProperty


```
Property {PROPERTY} has undeclared type {TYPE}
```

This issue will be emitted from the following code

```php
class D { /** @var Undef */ public $p; }
```

## PhanUndeclaredTypeReturnType

```
Return type of {METHOD} is undeclared type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0872_namespace_lookup.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0872_namespace_lookup.php#L4).

## PhanUndeclaredTypeThrowsType

```
@throws type of {METHOD} has undeclared type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0490_throws_suppress.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0490_throws_suppress.php#L11).

## PhanUndeclaredVariable


```
Variable ${VARIABLE} is undeclared
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0353_foreach_uncaught.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0353_foreach_uncaught.php#L3).

## PhanUndeclaredVariableAssignOp

```
Variable ${VARIABLE} was undeclared, but it is being used as the left-hand side of an assignment operation
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0841_assign_op_creates_variable.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0841_assign_op_creates_variable.php#L3).

## PhanUndeclaredVariableDim

```
Variable ${VARIABLE} was undeclared, but array fields are being added to it.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0135_array_assignment_type.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0135_array_assignment_type.php#L5).

# VarError

## PhanVariableUseClause

```
Non-variables ({CODE}) not allowed within use clause
```

# Generic

This category contains issues related to [Phan's generic type support](https://github.com/phan/phan/wiki/Generic-Types)

## PhanGenericConstructorTypes

```
Missing template parameter for type {TYPE} on constructor for generic class {CLASS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0203_generic_errors.php.expected#L8) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0203_generic_errors.php#L27).

## PhanGenericGlobalVariable

```
Global variable {VARIABLE} may not be assigned an instance of a generic class
```

## PhanTemplateTypeConstant

This is emitted when a class constant's PHPDoc contains a type declared in a class's phpdoc template annotations.

```
constant {CONST} may not have a template type
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0203_generic_errors.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0203_generic_errors.php#L8).

## PhanTemplateTypeNotDeclaredInFunctionParams

```
Template type {TYPE} not declared in parameters of function/method {FUNCTIONLIKE} (or Phan can't extract template types for this use case)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0597_template_support.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0597_template_support.php#L66).

## PhanTemplateTypeNotUsedInFunctionReturn

```
Template type {TYPE} not used in return value of function/method {FUNCTIONLIKE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0577_unknown_tags.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0577_unknown_tags.php#L20).

## PhanTemplateTypeStaticMethod

This is emitted when a static method's PHPDoc contains a param/return type declared in a class's phpdoc template annotations.

```
static method {METHOD} does not declare template type in its own comment and may not use the template type of class instances
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0203_generic_errors.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0203_generic_errors.php#L16).

## PhanTemplateTypeStaticProperty

This is emitted when a static property's PHPDoc contains an `@var` type declared in the class's phpdoc template annotations.

```
static property {PROPERTY} may not have a template type
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0203_generic_errors.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0203_generic_errors.php#L11).

# Internal

This issue category comes up when there is an attempt to access an `@internal` element (property, class, constant, method, function, etc.) outside of the namespace in which it's defined.

This category is completely unrelated to elements being internal to PHP (i.e. part of PHP core or PHP modules).

## PhanAccessClassConstantInternal

This issue comes up when there is an attempt to access an `@internal` class constant outside of the namespace in which it's defined.

```
Cannot access internal class constant {CONST} defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0618_internal_in_root_ns.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0618_internal_in_root_ns.php#L38).

## PhanAccessClassInternal

This issue comes up when there is an attempt to access an `@internal` class constant outside of the namespace in which it's defined.

```
Cannot access internal {CLASS} defined at {FILE}:{LINE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0278_internal_elements.php.expected#L24) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0278_internal_elements.php#L108).

## PhanAccessConstantInternal

This issue comes up when there is an attempt to access an `@internal` global constant outside of the namespace in which it's defined.

```
Cannot access internal constant {CONST} of namespace {NAMESPACE} defined at {FILE}:{LINE} from namespace {NAMESPACE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0278_internal_elements.php.expected#L16) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0278_internal_elements.php#L94).

## PhanAccessMethodInternal

This issue comes up when there is an attempt to access an `@internal` method outside of the namespace in which it's defined.

```
Cannot access internal method {METHOD} of namespace {NAMESPACE} defined at {FILE}:{LINE} from namespace {NAMESPACE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0618_internal_in_root_ns.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0618_internal_in_root_ns.php#L39).

## PhanAccessPropertyInternal

This issue comes up when there is an attempt to access an `@internal` property outside of the namespace in which it's defined.

```
Cannot access internal property {PROPERTY} of namespace {NAMESPACE} defined at {FILE}:{LINE} from namespace {NAMESPACE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0618_internal_in_root_ns.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0618_internal_in_root_ns.php#L37).

# CommentError

This is emitted for some (but not all) comments which Phan thinks are invalid or unparsable.

## PhanCommentAmbiguousClosure

```
Comment {STRING_LITERAL} refers to {TYPE} instead of \Closure - Assuming \Closure
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0524_closure_ambiguous.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0524_closure_ambiguous.php#L18).

## PhanCommentDuplicateMagicMethod

```
Comment declares @method {METHOD} multiple times
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0281_magic_method_support.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0281_magic_method_support.php#L17).

## PhanCommentDuplicateMagicProperty

```
Comment declares @property* ${PROPERTY} multiple times
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0612_comment_duplicated_property.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0612_comment_duplicated_property.php#L5).

## PhanCommentDuplicateParam

```
Comment declares @param ${PARAMETER} multiple times
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0611_comment_duplicated_param.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0611_comment_duplicated_param.php#L7).

## PhanCommentOverrideOnNonOverrideConstant

```
Saw an @override annotation for class constant {CONST}, but could not find an overridden constant
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0332_override_complex.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0332_override_complex.php#L10).

## PhanCommentOverrideOnNonOverrideMethod

```
Saw an @override annotation for method {METHOD}, but could not find an overridden method and it is not a magic method
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0355_namespace_relative.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0355_namespace_relative.php#L34).

## PhanCommentParamAssertionWithoutRealParam

```
Saw an @phan-assert annotation for ${PARAMETER}, but it was not found in the param list of {FUNCTIONLIKE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/086_comment_param_assertions.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/086_comment_param_assertions.php#L14).

## PhanCommentParamOnEmptyParamList

```
Saw an @param annotation for ${PARAMETER}, but the param list of {FUNCTIONLIKE} is empty
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/086_comment_param_assertions.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/086_comment_param_assertions.php#L3).

## PhanCommentParamOutOfOrder

```
Expected @param annotation for ${PARAMETER} to be before the @param annotation for ${PARAMETER}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0520_spaces_in_union_type.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0520_spaces_in_union_type.php#L5).

## PhanCommentParamWithoutRealParam

```
Saw an @param annotation for ${PARAMETER}, but it was not found in the param list of {FUNCTIONLIKE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0373_reject_bad_type_narrowing.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0373_reject_bad_type_narrowing.php#L4).

## PhanDebugAnnotation

```
@phan-debug-var requested for variable ${VARIABLE} - it has union type {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0772_real_float_types.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0772_real_float_types.php#L3).

## PhanInvalidCommentForDeclarationType

```
The phpdoc comment for {COMMENT} cannot occur on a {TYPE}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0432_phan_comment.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0432_phan_comment.php#L5).

## PhanMisspelledAnnotation

```
Saw misspelled annotation {COMMENT}. {SUGGESTION}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0301_comment_checks.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0301_comment_checks.php#L7).

## PhanThrowTypeAbsent

```
{FUNCTIONLIKE} can throw {CODE} of type {TYPE} here, but has no '@throws' declarations for that class
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/040_if_assign.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/040_if_assign.php#L4).

## PhanThrowTypeAbsentForCall

```
{FUNCTIONLIKE} can throw {TYPE} because it calls {FUNCTIONLIKE}, but has no '@throws' declarations for that class
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/043_throws.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/043_throws.php#L22).

## PhanThrowTypeMismatch

```
{FUNCTIONLIKE} throws {CODE} of type {TYPE} here, but it only has declarations of '@throws {TYPE}'
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/123_throw_static.php.expected#L4) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/123_throw_static.php#L14).

## PhanThrowTypeMismatchForCall

```
{FUNCTIONLIKE} throws {TYPE} because it calls {FUNCTIONLIKE}, but it only has declarations of '@throws {TYPE}'
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/085_throw_type_mismatch.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/085_throw_type_mismatch.php#L23).

## PhanUnextractableAnnotation

```
Saw unextractable annotation for comment '{COMMENT}'
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0828_crash_return_backslashes.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0828_crash_return_backslashes.php#L4).

## PhanUnextractableAnnotationElementName

```
Saw possibly unextractable annotation for a fragment of comment '{COMMENT}': after {TYPE}, did not see an element name (will guess based on comment order)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0284_non_empty_array_default.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0284_non_empty_array_default.php#L2).

## PhanUnextractableAnnotationPart

```
Saw unextractable annotation for a fragment of comment '{COMMENT}': '{COMMENT}'
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0627_signature_mismatch.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0627_signature_mismatch.php#L22).

## PhanUnextractableAnnotationSuffix

```
Saw a token Phan may have failed to parse after '{COMMENT}': after {TYPE}, saw '{COMMENT}'
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0670_unparseable_property.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0670_unparseable_property.php#L4).

# Syntax

Emitted for syntax errors.

## PhanContinueOrBreakNotInLoop

```
'{OPERATOR}' not in the 'loop' or 'switch' context.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0601_continue_scope_warning.php.expected#L5) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0601_continue_scope_warning.php#L24).

## PhanContinueOrBreakTooManyLevels

```
Cannot '{OPERATOR}' {INDEX} levels.
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0601_continue_scope_warning.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0601_continue_scope_warning.php#L6).

## PhanContinueTargetingSwitch

This detects code causing a [warning in PHP 7.3](http://php.net/manual/en/migration73.incompatible.php#migration73.incompatible.core.continue-targeting-switch).

```
"continue" targeting switch is equivalent to "break". Did you mean to use "continue 2"?
```


## PhanDuplicateUseConstant

```
Cannot use constant {CONST} as {CONST} because the name is already in use
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0723_duplicate_use.php.expected#L3) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0723_duplicate_use.php#L8).

## PhanDuplicateUseFunction

```
Cannot use function {FUNCTION} as {FUNCTION} because the name is already in use
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0723_duplicate_use.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0723_duplicate_use.php#L6).

## PhanDuplicateUseNormal

```
Cannot use {CLASSLIKE} as {CLASSLIKE} because the name is already in use
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0723_duplicate_use.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0723_duplicate_use.php#L4).

## PhanInvalidConstantExpression

```
Constant expression contains invalid operations
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/015_class_const_declaration9.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/015_class_const_declaration9.php#L3).

## PhanInvalidNode

```
%s
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/026_invalid_assign.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/026_invalid_assign.php#L2).

## PhanInvalidTraitUse

```
Invalid trait use: {DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/expected/056_trait_use.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/misc/fallback_test/src/056_trait_use.php#L11).

## PhanInvalidWriteToTemporaryExpression

```
Cannot use temporary expression ({CODE} of type {TYPE}) in write context
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0518_crash_assignment.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0518_crash_assignment.php#L4).

## PhanSyntaxCompileWarning

```
Saw a warning while parsing: {DETAILS}
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/157_polyfill_compilation_warning.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/157_polyfill_compilation_warning.php#L77).

## PhanSyntaxEmptyListArrayDestructuring

```
Cannot use an empty list in the left hand side of an array destructuring operation
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0799_array_destructuring_failures.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0799_array_destructuring_failures.php#L3).

## PhanSyntaxError

This emits warnings for unparsable PHP files (detected by `php-ast`).
Note: This is not the same thing as running `php -l` on a file - PhanSyntaxError checks for syntax errors, but not semantics such as where certain expressions can occur (Which `php -l` would check for).

Note: If the native parser is used, the reported column is a guess. Phan will use the column of the error reported by the **polyfill** if the errors are on the same line.

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/expected/136_unexpected_bracket.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/plugin_test/src/136_unexpected_bracket.php#L2).

## PhanSyntaxMixedKeyNoKeyArrayDestructuring

```
Cannot mix keyed and unkeyed array entries in array destructuring assignments ({CODE})
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0799_array_destructuring_failures.php.expected#L2) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0799_array_destructuring_failures.php#L4).

## PhanSyntaxReturnExpectedValue

```
Syntax error: Function {FUNCTIONLIKE} with return type {TYPE} must return a value (did you mean "{CODE}" instead of "{CODE}"?)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0242_void_71.php.expected#L6) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0242_void_71.php#L6).

## PhanSyntaxReturnValueInVoid

```
Syntax error: {TYPE} function {FUNCTIONLIKE} must not return a value (did you mean "{CODE}" instead of "{CODE}"?)
```

e.g. [this issue](https://github.com/phan/phan/tree/3.0.0/tests/files/expected/0242_void_71.php.expected#L1) is emitted when analyzing [this PHP file](https://github.com/phan/phan/tree/3.0.0/tests/files/src/0242_void_71.php#L3).

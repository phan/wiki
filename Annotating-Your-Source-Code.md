There are plenty of issues that Phan can find if you don't have type annotations in your code, but Phan can do a lot more for you if you tell it about your expectations.

Phan supports the following doc block annotations.

* [`@var <union_type>`](#var)
* [`@param <union_type> <variable_name>`](#param)
* [`@return <union_type>`](#return)
* [`@method <union_type> <method_name>(<union_type> <param1_name>)`](#method)
* [`@deprecated`](#deprecated)
* [`@internal`](#internal)
* [`@suppress <issue_type>`](#suppress)
* [`@property <union_type> <variable_name>`](#property)
* [`@override`](#override) (Since 0.9.3/0.8.5)
* [`@phan-closure-scope`](#override) (Since 0.9.3/0.8.5)

Additionally, Phan supports [inline type checks](#inline-type-checks), and can analyze `assert` statements and the conditionals of `if` statements and ternary operators.

## Inline Type Checks

Phan can analyze `assert()` statements and conditional branches to infer the types of variables within a block of code.
This is one way of working around [limitations on analyzing comment doc blocks](#invalid).

- Examples: `$x = someDynamicFactory(MyClass::class); assert($x instanceof MyClass)`, `assert(is_string($x))`, `assert(!is_null($x))`
- Phan can infer the same types for the condition of `if()` statements and ternary operators as it would from `assert`.

  Examples: `if ($x instanceof MyClass) { function_expecting_myclass($x); }`, `$result = ($x instanceof MyClass) ? function_expecting_myclass($x) : null`
- Be aware that incorrect `assert` statements may cause [unpredictable behavior in your code](https://secure.php.net/manual/en/function.assert.php#refsect2-function.assert-unknown-descriptioo)


## @var
The `@var <union_type>` annotation describes the type of the constant or property.

```php
class C
   /** @var int */
   const C = 42;

   /** @var DateTime|null */
   private $p = null;

   /** @var string $x */
   function foo() {
       // ...
   }
```

## @param

The `@param <union_type> <variable_name>` describes the type of a parameter.

```php
class C
   /**
    * @param int $a
    * @param ?string $b
    * @param DateTime $c
    */
   public function f($a, $b, $c) {
   }
```

## @return

The `@return <union_type>` annotation describes the return type of a method.

```php
class C {
   /**
    * @return null|int|string
    */
   public function f() {
     if (rand(1,10) < 5) {
         return null;
     }
     return rand(0,1) ?: 'string';
   }
```

## @property

The `@property <union_type> $prop_name` annotation describes a magic property of a class.
Partial support for this annotation was added in phan 0.9.1.

```php
/** @property int $magic_prop comment can go here */
class C {
   public function __get($name) {
       if ($name === 'magic_prop') { return 42; }
   }
   public function __set($name, $value) {}
}
```

Currently, `@property-read` and `@property-write` are aliases of `@property`, but those annotations may be improved in future releases of Phan.

Additionally, `@phan-forbid-undeclared-magic-properties` can be added to the class phpdoc (of a class with magic getters and setters) to indicate that phan should warn if any undeclared magic properties of that class were used elsewhere.

Phan does not yet enforce that `__get()`/`__set()` exist if a class/trait/interface declares magic properties.

## @method

The `@method <union_type> <method_name>(<union_type> <param1_name>)` annotation describes a magic property of a class.
Partial support for this annotation was added in phan 0.9.2/0.8.4.

```php
/**
 * @method int fooWithReturnType() - Can be followed by optional description
 * @method doSomething() implicitly has the void return type
 * @method mixed doSomething() use this if you don't know the return type, or if the return type can be anything.
 * @method fooWithOptionalParam(int, $b=null) - It's possible to leave out the name or the type, but not both.
 * @method fooWithOptionalNullableParam(string $x= null) The param $x is a nullable string. Null is the only value for which the default affects param type inference.
 * @method static static_foo() This is a static method returning void.
 * @method static int static_foo_with_return_type() - This is a static method returning int.
 * @method static static static_foo_with_return_type_of_static() This is a static method returning an instance of a class implementing this interface.
 * @method int myMethodWithUntypedParams($x) - Types are optional.
 * @method int myMethodWithPHPDocParams(double $x, object|null $y) - PHPDoc types and union types can be used.
 * @method int|string myMethodWithVariadicParams(int $a, int|string   ... $x ) ... and variadic types can be used.
 */
interface MagicInterface {
   // Non-abstract classes implementing this should define __call and/or __callStatic, but phan doesn't enforce that yet.
}
```

If Phan can't parse an @method annotation or the parameters, it will silently ignore the entire method annotation. A new issue type will likely be added in the future.

Phan also supports the `@phan-forbid-undeclared-magic-methods` annotation on classes (Support for inheriting this annotation for interfaces/traits is incomplete).
This will cause Phan to warn about a method being undefined if there are no real or phpdoc declarations of that method.
This can be used if you are concerned about magic methods being misspelled, being undocumented, or being used without the information Phan needs to type check their uses.

The behavior of Phan when a real method replaces a magic method is not yet fully defined. See https://github.com/etsy/phan/issues/670

## @deprecated

You can mark a function, method or class as deprecated by adding `@deprecated` to its doc block.

```php
/** @deprecated */
class C {
    /** @deprecated */
    public function f() {}
}

/** @deprecated */
function g() {}
```

## @internal

The `@internal` annotation marks an element (such as a constant, function, class, class constant, property or method) as internal to the package in which its defined.

Any element marked as `@internal` and accessed outside of its exact namespace (disallowing both child and parent namespaces) will result in Phan emitting an issue.

```php
<?php
namespace NS\A {

    const CONST_PUBLIC = 41;

    /** @internal */
    const CONST_INTERNAL = 42;

    /** @internal */
    class C1 {
        const CONST_INTERNAL = 42;
        public $p;
        public function f() {}
    }

    class C2 {
        const CONST_PUBLIC = 41;

        /** @internal */
        const CONST_INTERNAL = 42;

        /** @internal */
        public $p;

        /** @internal */
        public function f() {}

        public function g() {}
    }

    /** @internal */
    function f() {}

    interface I1 {}
    /** @internal */
    interface I2 {}
    trait T1 {}
    /** @internal */
    trait T2 {}

    class C3 extends C1 implements I1, I2 {
        use T1, T2;
}

namespace NS\B {
    use const NS\A\CONST_PUBLIC;
    use const NS\A\CONST_INTERNAL;
    use function NS\A\f;
    use NS\A\C1;
    use NS\A\C2;
    use NS\A\I1;
    use NS\A\I2;
    use NS\A\T1;
    use NS\A\T2;

    $v01 = f();                             // PhanAccessMethodInternal
    $v02 = CONST_PUBLIC;
    $v03 = CONST_INTERNAL;                  // PhanAccessConstantInternal

    $v1 = new C1;                           // PhanAccessMethodInternal
    $v11 = $v1->p;                          // PhanAccessPropertyInternal
    $v12 = $v1->f();                        // PhanAccessMethodInternal
    $v13 = C1::CONST_INTERNAL;              // PhanAccessClassConstantInternal

    $v2 = new C2;
    $v21 = $v2->p;                          // PhanAccessPropertyInternal
    $v22 = $v2->f();                        // PhanAccessMethodInternal
    $v23 = $v2->g();
    $v24 = C2::CONST_PUBLIC;
    $v25 = C2::CONST_INTERNAL;              // PhanAccessClassConstantInternal

    class C3 extends C1 implements I1, I2 { // PhanAccessClassInternal x 2
        use T1, T2;                         // PhanAccessClassInternal
    }
}
```

## @suppress

Phan issue types may be suppressed by adding `@suppress <issue_type>` to the doc block of the closest function, method or class scope to where the issue is emitted.

For example; the following will work.

```php
class D {
    /**
     * @suppress PhanUndeclaredClassMethod
     */
    function g() {
        C::f();
    }
}
```

But the following will not suppress the issue.

```php
/** @suppress PhanUndeclaredClassMethod */
class D {
    // @suppress PhanUndeclaredClassMethod
    function g() {
        /** @suppress PhanUndeclaredClassMethod */
        C::f();
    }
}
```

This is because the first `@suppress` is on the class and is not the closest scope to the call, the second is not in a valid doc block, and the third is on a comment next to the call, not its closest scope.

This will bite you and I apologize.

## @override

Supported in Phan 0.9.3+/0.8.5+

Phan will check places where `@override` is mentioned to see if the method is actually overriding a definition or implementing an abstract method (Or a phpdoc `@method`) in an ancestor class/trait/interface.

Additionally, in php 7.1+ (due to limitations of php-ast's implementation), Phan will check that class constants marked with `@override` override the definition of a constant in an ancestor class/interface/trait.

Phan will warn if the element doesn't actually override anything. Other than that check, the annotation does not affect analysis.

`@Override` and `@phan-override` are supported aliases of `@override`.

The `@override` annotation is [not part of any official PHPDoc standard](https://github.com/etsy/phan/issues/926) I'm aware of. The semantics are similar to Java's `@Override`.

```php
class BaseClass {
    const FOO = 'value';
    public function myMethod(int $x) { return false; }
}

class SubClass extends BaseClass {
    /** @override (valid use for Phan, checked in php 7.1+) */
    const FOO = 'otherValue';

    /** @override (valid use for Phan) */
    public function myMethod(int $x) { return true; }
}
```

## @phan-closure-scope

Supported in Phan 0.9.3+/0.8.5+

This can be used when a closure is intended to be bound to a class that is different from the class in which it was declared.
This helps avoid false positive warnings about PhanUndeclaredProperty, etc.

```php
class MyClass {
    private function privateMethod(int $x) {}
}
/** @phan-closure-scope MyClass - Phan analyzes the inner body of this closure as if it were a closure declared in MyClass. */
$c = function(int $arg) {
    return $this->privateMethod($arg);
};
// ...Do stuff...

// This is an example of the eventual use, which may be in a completely different file
$m = new MyClass();
$invoker = $c->bindTo($m);
$invoker(2);
```

# Doc Blocks

Doc blocks must be enclosed in comments of the form `/** ... */` starting with `/**` and ending with `*/`. Annotations won't be read from comments with any other format. This will cause you frustration.

Furthermore, Phan can only see doc block comments on

* classes
* constants
* properties
* methods
* functions

Doc blocks will not be read from any other elements (such as any kind of statement).

## Valid Doc Blocks

```php
/**
 * @return void
 */
function f() {}

/** @deprecated */
class C {
    /** @var int */
    const C = 42;

    /** @var ?string */
    public $p = null;

    /**
     * @param int|null $p
     * @return int|null
     */
    public static function f($p) {
        return $p;
    }
}
```

```php
/** @return void */
function f() {}
```

## Invalid

In the example below, we have the wrong comment type.

```php
// @return void
function f() {}
```

Similarly, a single asterisk at the beginning won't cut it. This will burn you and I apologize.

```php
/* @return void */
function f() {}
```

In the case below, we have a valid comment type, but its not on a class, constant, property, method or function, so it will not be read.

```php
function f() {
    /** @suppress PhanUndeclaredClassMethod */
    Undef::g();
}
```
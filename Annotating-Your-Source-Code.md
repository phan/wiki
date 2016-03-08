There are plenty of issues that Phan can find if you don't have type annotations in your code, but Phan can do a lot more for you if you tell it about your expectations.

Phan supports the following doc block annotations.

* `@var <union_type>`
* `@param <union_type> <variable_name>`
* `@return <union_type>`
* `@deprecated`
* `@suppress <issue_type>`

## @var
The `@var <union_type>` annotation describes the type of the constant, property or variable being referenced such as in

```php
class C
   /** @var int */
   const C = 42;

   /** @var DateTime|null */
   private $p = null;

   /** @var string $v */
   public function f() {
       $v = $_GET['thing'];
   }
```

## @param

The `@param <union_type> <variable_name>` describes the type of a parameter.

```php
class C
   /**
    * @param int $a
    * @param string|null $b
    * @param DateTime $c
    */
   public function f($a, $b, $c) {
   }
```

## @return

The `@return <union_type>` annotation describes the return type of a method.

```php
class C
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

    /** @var string|null */
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
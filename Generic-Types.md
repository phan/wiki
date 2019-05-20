Phan has primordial support for generic (templated) classes via type the annotations `@template` and `@inherits` and via a type syntax of the form `MyClass<T>` that may be referenced within doc-block annotations.

For details on possible language-level support for generics, take a look at the draft RFC for [Generic Types and Functions](https://wiki.php.net/rfc/generics).

The current implementation is very incomplete. You'll likely end up being annoyed that we don't yet allow for generic interfaces or defining constraints on types. There are also very likely to be significant bugs with the current implementation.

- There is also support for declaring templates on function-likes (functions, methods, and closures) based on their parameters. See the section [Function-Like Templates](#function-templates).

# Rules for using Generics

Support for generics within Phan is not the same as other languages and comes with its own special rules.

## Declare Template Types

All template types for a generic class must be declared on the class via doc block comments via the `@template` annotation.

The annotation on the class below shows how template types can be defined.

```php
/** @template T */
class C {

    /** @var T */
    protected $p;

    /** @param T $p */
    public function __construct($p) {
        $this->p = $p;
    }
}
```

## The Constructor Must Make All Types Concrete
All template types (of a generic class) must be filled in with concrete types via the constructor.

In the example above, the template type `T` is declared for the `__constructor` method. An issue will be emitted by Phan for any template types that aren't specified as parameters on the constructor with the following form.

```
%s:%d PhanGenericConstructorTypes Missing template parameters %s on constructor for generic class %s
```

## Extending Classes Must Fill In Template Types
Classes extending generic classes need to fill in the types of the generic parent class via the `@inherits` annotation.

The example below builds off of the definition of `C` from the example above to show how to extend a generic class.

```php
/**
 * @inherits C<int>
 */
class C1 extends C {
    public function __construct(int $p) {
        $this->p = $p;
    }
}
```

You can carry template types through to a sub-class by defining a new template type and assigning that to the parent class such as in the example below.

```php
/**
 * @template T2
 *
 * @inherits C<T2>
 */
class C2 extends C {
    /** @param T2 $p */
    public function __construct($p) {
        $this->p = $p;
    }
}
```

## No Generic Statics

Constants and static methods on a generic class cannot reference template types of the generic class.

See the section [Function-Like Templates](#function-templates) for an alternative for static (and instance) methods.

# How It Works

When a class is made generic by declaring template types, the Class's scope will be made aware of the template types so that properties and methods can refer to them via `@var`, `@param` and `@return`. When the class is analyzed, checks will be done by assuming that the template types are some undefined class.

Given the rule that a generic class needs to have all templates mapped to concrete types in the constructor, Phan will express the type of `new C(...)` not as `Type('C')`, but as `Type('C<T>')` for parameter type `T`. This way, whenever a property is referenced or a method is called on the instance, the declared type of the property or return type of the method will be mapped from template types to concrete types using the map on the `Type` associated with the instance.

# Example Generic Classes

Phan comes with a library that contains a few generic classes such as [Option](https://github.com/phan/phan/blob/master/src/Phan/Library/Option.php), [Some](https://github.com/phan/phan/blob/master/src/Phan/Library/Some.php), [None](https://github.com/phan/phan/blob/master/src/Phan/Library/None.php), [Tuple2](https://github.com/phan/phan/blob/master/src/Phan/Library/Tuple2.php), [Set](https://github.com/phan/phan/blob/master/src/Phan/Library/Set.php), and others.

The following implementation of `Tuple2` shows off how generics work.

```php
<?php declare(strict_types=1);

/**
 * A tuple of 1 element.
 *
 * @template T0
 * The type of element zero
 */
class Tuple1 extends Tuple
{
    /** @var int */
    const ARITY = 1;

    /** @var T0 */
    public $_0;

    /**
     * @param T0 $_0
     * The 0th element
     */
    public function __construct($_0) {
        $this->_0 = $_0;
    }

    /**
     * @return int
     * The arity of this tuple
     */
    public function arity() : int
    {
        return static::ARITY;
    }

    /**
     * @return array
     * An array of all elements in this tuple.
     */
    public function toArray() : array
    {
        return [
            $this->_0,
        ];
    }
}

/**
 * A tuple of 2 elements.
 *
 * @template T0
 * The type of element zero
 *
 * @template T1
 * The type of element one
 *
 * @inherits Tuple1<T0>
 */
class Tuple2 extends Tuple1
{
    /** @var int */
    const ARITY = 2;

    /** @var T1 */
    public $_1;

    /**
     * @param T0 $_0
     * The 0th element
     *
     * @param T1 $_1
     * The 1st element
     */
    public function __construct($_0, $_1) {
        parent::__construct($_0);
        $this->_1 = $_1;
    }

    /**
     * @return array
     * An array of all elements in this tuple.
     */
    public function toArray() : array
    {
        return [
            $this->_0,
            $this->_1,
        ];
    }
}
```

As a further example, here's what an implementation of `Option`, `Some` and `None` might look like.

```php
<?php declare(strict_types=1);

/**
 * @template T
 * The type of the element
 */
abstract class Option
{
    /**
     * @param T $else
     * @return T
     */
    abstract public function getOrElse($else);

    /**
     * @return bool
     */
    abstract public function isDefined() : bool;

    /**
     * @return T
     */
    abstract public function get();
}

/**
 * @template T
 * The type of the element
 *
 * @inherits Option<T>
 */
class Some extends Option
{
    /** @var T */
    private $_;

    /**
     * @param T $_
     */
    public function __construct($_)
    {
        $this->_ = $_;
    }

    /**
     * @return bool
     */
    public function isDefined() : bool
    {
        return true;
    }

    /**
     * @return T
     */
    public function get()
    {
        return $this->_;
    }

    /**
     * @param T $else
     * @return T
     */
    public function getOrElse($else)
    {
        return $this->get();
    }

    /**
     * @return string
     * A string representation of this object
     */
    public function __tostring() : string
    {
        return 'Some(' . $this->_ . ')';
    }
}

/**
 * @inherits Option<null>
 */
class None extends Option
{
    /**
     * Get a new instance of nothing
     */
    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function isDefined() : bool
    {
        return false;
    }

    /**
     * @param mixed $else
     * @return mixed
     */
    public function getOrElse($else)
    {
        return $else;
    }

    /**
     * @return null
     */
    public function get()
    {
        throw new \Exception("Cannot call get on None");
    }

    /**
     * @return string
     * A string representation of this object
     */
    public function __tostring() : string
    {
        return 'None()';
    }
}
```

# Function templates

Templates can be inferred from arguments on function-like elements.
These are useful for inferring the value returned by a function/closure/method.
Improvements and bug fixes for this will be included in subsequent releases.

Phan can now infer template types in regular functions/methods. For example, it can infer the types of a template `T` from other types (both in Generics and when inferring return types)

- simple, e.g. `@param T $argName`
- array values, e.g. `@param T[] $argName`, `@param array{myFieldName:T} $argName`
- return types, e.g. `@param Closure():T $argName`, `@param callable(T):bool $argName`
- templates of other generics, e.g. `@param OtherClass<\stdClass,T> $argName`
- class names, e.g. `@param class-string<T> $argName`

Note that this implementation is currently incomplete - Phan is not yet able to extract `T` from types not mentioned here (e.g. `Generator<T>`, etc.)

Phan will take the union of all types inferred for `T` (without checking if the parameters are compatible)

## Indicating a function returns elements of an array

```php
class ArrayUtil {
    /**
     * @template T
     * @param T[] $x
     * @return T
     * @throws InvalidArgumentException
     */
    public static function firstElement(array $x) {
        if (count($x) == 0) {
            throw new InvalidArgumentException("Expected one element");
        }
        return reset($x);
    }
}
// Phan will that this must be an stdClass because of this annotation.
$result = ArrayUtil::firstElement([new stdClass()]);
```

## Indicating a function depends on the return type of a closure

```php
function identity($x) {
    var_dump($x);
    return $x;
}

/**
 * @template T
 * @param Closure(int $i):T $x
 * @return array<int,T>
 */
function example_closure_usage(Closure $x) {
    $result = [];
    for ($i = 0; $i < 10; $i++) {
        // Extra code that makes this harder to analyze recursively
        $result[] = identity($x($i));
    }
    return $result;
}

// Phan will infer from the closure's return type
// that $result is an `array<int,int>` instead of something vaguer
$result = example_closure_usage(function (int $i) : int {
    return rand(0, abs($i));
});
```

## Indicating a return type depends on template types of another generic

See [this file](https://github.com/phan/phan/blob/2.0.0/tests/files/src/0600_template_from_template.php) and [the example detected issues](https://github.com/phan/phan/blob/2.0.0/tests/files/expected/0600_template_from_template.php.expected)

## Indicating a method creates an instance of the passed in class name

This requires using the type `class-string<T>` type. This indicates that the argument is a string: the name of the class `T` of the object that will be returned.

```php
<?php
/**
 * @template T
 * @param class-string<T> $name
 * @return T
 */
function my_object_factory(string $name) {
    // The same annotation can be used for complex factories
    return new $name;
}
$result = my_object_factory('stdClass');  // Phan infers that this is an stdClass
$other = my_object_factory(MyClass::class);  // Phan infers that this is an instance of MyClass
```

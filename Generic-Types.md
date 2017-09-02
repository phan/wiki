Phan has primordial support for generic (templated) classes via type the annotations `@template` and `@inherits` and via a type syntax of the form `Class<T>` that may be referenced within doc-block annotations.

For details on possible language-level support for generics, take a look at the draft RFC for [Generic Types and Functions](https://wiki.php.net/rfc/generics).

The current implementation is very incomplete. You'll likely end up being annoyed that we don't yet allow for generic interfaces, generic functions/methods, or defining constraints on types. There are also very likely to be significant bugs with the current implementation.

# Rules for using Generics

Support for generics within Phan is not the same as other languages and comes with its own special rules.

## Declare Template Types
All template types must be declared on the class via doc block comments via the `@template` annotation.

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
All template types must be filled in with concrete types via the constructor.

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
Constants and static methods on a generic class cannot reference template types.

# How It Works

When a class is made generic by declaring template types, the Class's scope will be made aware of the template types so that properties and methods can refer to them via `@var`, `@param` and `@return`. When the class is analyzed, checks will be done by assuming that the template types are some undefined class.

Given the rule that a generic class needs to have all templates mapped to concrete types in the constructor, Phan will express the type of `new C(...)` not as `Type('C')`, but as `Type('C<T>')` for parameter type `T`. This way, whenever a property is referenced or a method is called on the instance, the declared type of the property or return type of the method will be mapped from template types to concrete types using the map on the `Type` associated with the instance.

# Example Generic Classes

Phan comes with a library that contains a few generic classes such as [Option](https://github.com/phan/phan/blob/master/src/Phan/Library/Option.php), [Some](https://github.com/phan/phan/blob/master/src/Phan/Library/Some.php), [None](https://github.com/phan/phan/blob/master/src/Phan/Library/None.php), [Tuple2](https://github.com/phan/phan/blob/master/src/Phan/Library/Tuple2.php), and others.

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
        return null;
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
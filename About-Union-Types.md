Union types can be any native type such as `int`, `string`, `bool`, or `array`, any class such as `DateTime`, arrays of types such as `string[]`, `DateTime[]` or a union of any other types such as `string|int|null|DateTime|DateTime[]`.

All of the following are valid union types;

* `int`
* `int|float`
* `int[]|float[]|null`
* `DateTime`
* `DateTime|string|int`
* `null`

As a special case, `void` may be used as a return type indicating that the function or method is not expected to return anything. In practice, this still implies that the function or method returns null, but Phan will enforce that there is not an explicit return.

The special cases `static` and `self` are also supported as return types on methods which specify that the return type is a late-statically-bound version of the class, or the class in which the method is defined respectively.

# Example Union Type Annotations

The following code represents a variety of union type annotations that Phan respects.

```php
<?php

class D {

    /** @var string */
    const CONSTANT = 'constant';

    /** @var DateTime|string|int|null */
    private $date = null;

    /**
     * @param DateTime|string|int|null $date
     */
    public function __construct($date) {
        $this->date = $date;
    }

    /**
     * @return DateTime
     */
    public function getDateTime() {

        if (is_null($this->date)) {
            return new DateTime();
        }

        if ($this->date instanceof DateTime) {
            return $this->date;
        }

        if (is_int($this->date)) {
            $d = new DateTime;
            $d->setTimestamp($this->date);
            return $d;
        }

        if (is_string($this->date)) {
            return new DateTime($this->date);
        }

        return new DateTime($this->date);
    }

    /**
     * @return string
     *
     * @var DateTime $date_time
     */
    public function getString() {
        $date_time = $this->getDateTime();
        return $date_time->format('M d Y');
    }
}
```

# Union Types as a BNF

A union type is expressed as a pipe ('|') delimited list of types which can be scalars, arrays, arrays of a type, or classes.
Phan supports nullable types.

```
UNION_TYPE     : TYPE
               | TYPE '|' UNION_TYPE
               |
               ;

TYPE           : NON_NULL_TYPE
               | '?' NON_NULL_TYPE

NON_NULL_TYPE  : CLASS_NAME
               | NATIVE_TYPE
               | ARRAY_TYPE
               | SPECIAL_TYPE
               ;

ARRAY_TYPE     | NON_NULL_TYPE '[]'
               ;

CLASS_NAME     : string
               ;

NATIVE_TYPE    : 'int'
               | 'float'
               | 'string'
               | 'bool'
               | 'iterable'
               | 'array'
               | 'mixed'
               | 'callable'
               | 'resource'
               | 'null'
               ;

SPECIAL_TYPE   : 'void'
               | 'static'
               | 'self'
               | 'object'
               | 'mixed'
               ;

```
Union types can be any native type such as `int`, `string`, `bool`, or `array`, any class such as `DateTime`, arrays of types such as `string[]`, `DateTime[]` or a union of any other types such as `string|int|null|DateTime|DateTime[]`.

All of the following are valid union types;

* `int`
* `int|float`
* `int[]|float[]|null`
* `DateTime`
* `DateTime|string|int`
* `?int|?DateTime` (Note that `?T` in doc comments is not a part of the phpdoc2 standard. Phan analyzes this syntax more strictly than `int|DateTime|null`)
* `null`
* `resource|false`

Phan also supports these union types, both internally and in phpdoc annotations (These are not part of the phpdoc2 standard):

* `array<string,stdClass>` (an array with string keys and stdClass values)
* `array<mixed,float>` (equivalent to `array<int|string,float>` and `float[]`)
* `array{0:string,1:bool}` (The inferred type for an expression such as `['str', rand(0,2) > 0]`)
* `array{key:value}` (The inferred type for an expression such as `['key'=>'str']`)


As a special case, `void` may be used as a return type indicating that the function or method is not expected to return anything. In practice, this still implies that the function or method returns null, but Phan will enforce that there is not an explicit return.

The special cases `static` and `self` are also supported as return types on methods which specify that the return type is a late-statically-bound version of the class, or the class in which the method is defined respectively.

In Phan 0.10+/0.8.5+, `false` and `true` are treated as types that are separate from `bool`.
This change made Phan more effective at analyzing code such as `$x = fopen(...); if ($x){...}`

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

A union type is expressed as a pipe (`|`) delimited list of types, which can be native types (e.g. scalars, arrays), special types (e.g. `object`, `mixed`), arrays of a type, or classes.
Those types can be nullable.

TODO: Document generics, `array<key, value>`, `array<value>`, and `array{key:shape[...,keyN:shapeN]}`

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
               | 'false'
               | 'true'
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

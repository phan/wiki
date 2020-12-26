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

* `array<string,stdClass>` (an array with string keys and stdClass values.)
* `array<mixed,float>` (equivalent to `array<int|string,float>` and `float[]`)
* `non-empty-array<mixed,float>` (an array of floats, with at least one element)
* `list<object>` (a list of objects, with consecutive integer keys starting from 0. `list`s cannot cast to/from `associative-array` or `array<string,X>`)
* `non-empty-list<object>` (a non-empty list of objects, with consecutive integer keys starting from 0)
* `associative-array<int, stdClass>` (a map from integer keys to `stdClass` values)
* `non-empty-associative-array<mixed, float>` (a map from integer or string keys to floats, with at least one element)
* `array{0:string,1:bool}` (The inferred type for an expression such as `['str', rand(0,2) > 0]`)
  In Phan, this can also be written as `array{string,bool}` (which is currently analyzed the same way).
* `array{key:value}` (The inferred type for an expression such as `['key'=>$myValue]`)
* `array{key?:value}` (E.g. `@param array{key?:value} $options`, to indicate that a field with key `'key'` may or may not be defined)
* `callable(ParamUnionType):ReturnUnionType` can optionally be used in phpdoc to describe the parameter and return types expected for a callable

   (See [this example](https://github.com/phan/phan/blob/v4/tests/files/src/0457_callable_type_cast.php))

   The modifiers `=` (to indicate optional), `...` (to indicate variadic), and `&` can be used. Those modifiers must occur in the same order they'd occur in a function.
   E.g. `function(bool $optionalBool = false, int &...$refArgs): int {...}` can be cast to `callable(bool=,int&...):int`.

   Complex return union types must be surrounded by `()` to be parsed, e.g. `callable(): (int|false)`
* `Closure(ParamUnionType):ReturnUnionType`: Same syntax as `callable(ParamUnionType):ReturnUnionType`
  Can be prefixed with `\`. See [this example](https://github.com/phan/phan/blob/v4/tests/files/src/0455_closure_type_cast.php)
* `2` (The inferred type for an expression such as `1+1`)
* `'myvalue'` (The inferred type for an expression such as `"myvalue"`)
* `class-string` and `class-string<T>` (strings that are fully qualified names of classes)
* `callable-string`, `callable-object` and `callable-array` (strings, objects, and arrays that are also callable)

As a special case, `void` may be used as a return type indicating that the function or method is not expected to return anything. In practice, this still implies that the function or method returns null, but Phan will enforce that there is not an explicit return.

The special cases `static` and `self` are also supported as return types on methods which specify that the return type is a late-statically-bound version of the class, or the class in which the method is defined respectively.

- Phan currently treats `$this` the same way it treats `static`. Note that `$this` is only supported in return types.

`false` and `true` are treated as types that are separate from `bool`.
This makes Phan more effective at analyzing code such as `$x = fopen(...); if ($x){...}` (`fopen` returns `int|false`)

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

TODO: Document generics, `array<key, value>` (both UNION_TYPEs), `array<value>`, and `array{key:shape[...,keyN:shapeN]}`, and optional array keys, and typed closure/Callables.

TODO: Document `Traversable<...>`, `Generator<...`>, etc.

TODO: Document literal integers (`2`) and literal strings (`'a value'`)


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
               | '$this'
               ;

```

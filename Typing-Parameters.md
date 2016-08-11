PHP5 supports type hints on parameters. PHP7 supports proper types on parameters. Phan reads doc-block `@param` types. This article is meant to explain how they interact and when to use which system.

# Declared Types Override `@param` Types

Whenever parameters have a type or type-hint, `@param` types are ignored.

```php
/**
 * @param int $t
 */
function f(DateTime $t) { ... }
```

In the example above, the type of `$t` will be `DateTime` with the `int` declaration being ignored.

# Use `@param` for Union Types

If a parameter can be one of many types, you'll need to express this via the `@param` annotation and leave the parameter type or type-hint empty.

```php
/**
 * @param DateTime|int|null $t
 */
function f($t) { ... }
```

Since PHP does not support union types, you'll need to express a union type as in the example above.

# Use `@param` for Scalars on PHP5 Compatible Code

As of PHP7, parameters can be typed as scalars (such as `int` or `string`). This, however, doesn't exist in PHP5. If you'd like to type a parameter as a scalar in code that may run on a PHP5 runtime, you'll need to leave the type-hint empty such as in the example below.

```php
/**
 * @param int $t
 */
function f($t) { ... }
```


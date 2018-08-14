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

We prefer syntax-level types and type-hints because they're checked at runtime. Annotations are not.

# Use `@param` for Union Types

If a parameter can be one of many types, you'll need to express this via the `@param` annotation and leave the parameter type or type-hint empty.

NOTE: it is possible to use both `@param` annotations and type hints, if each type in the `@param` union type is a narrowed version of the type hint ( subclasses or subtypes). This requires the config settings `prefer_narrowed_phpdoc_param_types` and `check_docblock_signature_param_type_match` to be true (the default).

```php
/**
 * @param DateTime|int|null $t
 */
function f($t) { ... }
```

Since PHP does not support union types, you'll need to express a union type as in the example above.

Keep in mind that in this case you're trading off runtime type checking for something that is better able to express the state of the world, but is not checked in the running code. For this reason, you should use union types to express the state of pre-existing code, but shouldn't use them when creating new interfaces.

# Use `@param` for Scalars on PHP5 Compatible Code

As of PHP7, parameters can be typed as scalars (such as `int` or `string`). This, however, doesn't exist in PHP5. If you'd like to type a parameter as a scalar in code that may run on a PHP5 runtime, you'll need to leave the type-hint empty such as in the example below.

```php
/**
 * @param int $t
 */
function f($t) { ... }
```

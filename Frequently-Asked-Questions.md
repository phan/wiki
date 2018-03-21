## Table of Contents

- [Phan says that an internal class, constant, or function (e.g. xdebug_is_enabled, Soap, memcached, etc.) is undeclared](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#phan-says-that-an-internal-class-constant-or-function-eg-xdebug_is_enabled-soap-memcached-etc-is-undeclared)
- [Phan isn't picking up my project's doc comments](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#phan-isnt-picking-up-my-projects-doc-comments)
- [One of Phan's function or method signatures have incorrect parameter types or return types](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#one-of-phans-function-or-method-signatures-have-incorrect-parameter-types-or-return-types)
- [PHP 7.1 features such as nullable types aren't being parsed](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#php-71-features-such-as-nullable-types-arent-being-parsed)
- [Phan is warning about the code using PHP 7.1/7.2 features](https://github.com/phan/phan/wiki/Frequently-Asked-Questions/#phan-is-warning-about-the-codebase-using-syntax-that-is-incompatible-with-php-707172)
- [A variadic function with phpdoc has unexpected types](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#a-variadic-function-with-phpdoc-has-unexpected-types)
- There are [[Different Issue Sets On Different Numbers of CPUs]]
- **[How to file a bug report for a crash, error, or incorrect analysis](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#how-to-file-a-bug-report-for-a-crash-error-or-incorrect-analysis)**

## Common Questions

### Phan says that an internal class, constant, or function (e.g. xdebug_is_enabled, Soap, Memcached, etc.) is undeclared

For the best results, run Phan with the same extensions (and same extension versions) installed and enabled as you would in the project being enabled.

Phan 0.10.1 supports [internal stubs](https://github.com/phan/phan/wiki/How-To-Use-Stubs#internal-stubs). [Regular stubs](https://github.com/phan/phan/wiki/How-To-Use-Stubs#stubs) are another option for older Phan releases.

Phan automatically disables xdebug for performance reasons. If your project uses xdebug, enable the corresponding [internal stubs](https://github.com/phan/phan/wiki/How-To-Use-Stubs#internal-stubs) in your project's `.phan/config.php`.

### Phan isn't picking up my project's doc comments

See https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#doc-blocks (Common issues: Phan does not support inline doc comments, doc comments must begin with `/**`, etc)

### One of Phan's function or method signatures have incorrect parameter types or return types

Double check the documentation and examples provided on php.net (or for the latest stable version of that extension).

If Phan is inconsistent with the documentation, create a PR modifying the related functions/methods in https://github.com/phan/phan/blob/master/src/Phan/Language/Internal/FunctionSignatureMap.php (or file an issue).

### PHP 7.1 features such as nullable types aren't being parsed

Make sure that Phan is being invoked with php 7.1 (or newer). For best results, the PHP version should be close to the version of the project being analyzed.

The PHP version used to invoke Phan must be 7.1 or newer to parse php 7.1 code with `php-ast` (As well as for getting the real function and method signatures, etc.). `php-ast` uses the current PHP version's internal parse tree.

The CLI options `--force-polyfill-parser` and/or `--use-fallback-parser` may also be used, but the pure PHP parser implementation has bugs in a few edge cases. (and they're slower)

### Phan is warning about the codebase using syntax that is incompatible with php 7.0/7.1/7.2

These warnings were introduced in Phan 12.0. This can be solved by setting the `target_php_version` in your .phan/config.php to `'7.1'`/`'7.2'` (if that is the oldest php version your project supports), or by changing the code to stop using newer syntax. You may also suppress that issue in .phan/config.php, and various other ways.

+ `CompatibleNullableTypePHP70`, `CompatibleShortArrayAssignPHP70`, `CompatibleKeyedArrayAssignPHP70`,
  `CompatibleKeyedArrayAssignPHP70`, and `CompatibleIterableTypePHP70`
  are emitted when the `target_php_version` is less than '7.1'.
+ `CompatibleObjectTypePHP71` is emitted for the `object` typehint when the `target_php_version`
  is less than 7.2.


### A variadic function with phpdoc has unexpected types

The union type Phan reads for `@param` before `...` is the union type of individual elements that are passed by the caller, not the type within the function body. The below is an example of how a function should be documented.

```php
/** @param string ...$args (should be string, not string[] or array, and include "..." before the parameter name) */
function my_function(string ...$args) {}
my_function('arg1', 'other_arg');
```

See [the phpdocumentor2 implementation](https://github.com/phpDocumentor/ReflectionDocBlock/blob/14f9edf1ae14d6ce417afb05a9ed37d7b3cc341e/tests/unit/DocBlock/Tags/ParamTest.php#L152-L168) It parses the individual element types as `string` from `@param string ...$varName`. This detail of phpDocumentor2 is also documented in [the phpDocumentor/fig-standards repo](https://github.com/phpDocumentor/fig-standards/issues/40#issuecomment-138117263).

`@phan-param` can be used if you must use a different standard for documenting variadic $args with `@param`.

### There are Different Issue Sets On Different Numbers of CPUs

See [[Different Issue Sets On Different Numbers of CPUs]]

### How to file a bug report for a crash, error, or incorrect analysis

Install `dev-master` of Phan (e.g. from composer) and see if the issue still occurs. It may have been fixed recently.

If the issue continues to happen on `dev-master`, then:

1. Check for similar issues. If there is anything new to add, add that.
2. If there are no similar issues, then file an new issue with any of the relevant information:
   - stack trace for a crash
   - Code snippets (or a link to an affected project, if possible) and config settings (if any) needed to reproduce the issue.
   - Phan version used.
   - Observed behavior and expected behavior.
## Table of Contents

- [Phan says that an internal class or function (e.g. xdebug_is_enabled, Soap, memcached, etc.) is undefined](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#phan-says-that-an-internal-class-or-function-eg-xdebug_is_enabled-soap-memcached-etc-is-undefined)
- [Phan isn't picking up my project's doc comments](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#phan-isnt-picking-up-my-projects-doc-comments)
- [A crash or error with a stack trace has occurred](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#a-crash-or-error-with-a-stack-trace-has-occurred)
- [One of Phan's function or method signatures have incorrect parameter types or return types](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#one-of-phans-function-or-method-signatures-have-incorrect-parameter-types-or-return-types)
- [PHP 7.1 features such as nullable types aren't being parsed](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#php-71-features-such-as-nullable-types-arent-being-parsed)
- [A variadic function with phpdoc has unexpected types](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#a-variadic-function-with-phpdoc-has-unexpected-types)
- There are [[Different Issue Sets On Different Numbers of CPUs]]

## Common Questions

### Phan says that an internal class or function (e.g. xdebug_is_enabled, Soap, memcached, etc.) is undefined

For the best results, run Phan with the same extensions (and same extension versions) installed and enabled as you would in the project being enabled.

Phan 0.10.1 supports [internal stubs](https://github.com/phan/phan/wiki/How-To-Use-Stubs#internal-stubs). [Regular stubs](https://github.com/phan/phan/wiki/How-To-Use-Stubs#stubs) are another option for older Phan releases.

Phan automatically disables xdebug for performance reasons. If your project uses xdebug, enable the corresponding [internal stubs](https://github.com/phan/phan/wiki/How-To-Use-Stubs#internal-stubs) in your project's `.phan/config.php`.

### Phan isn't picking up my project's doc comments

See https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#doc-blocks (Common issues: Phan does not support inline doc comments, doc comments must begin with `/**`, etc)

### A crash or error with a stack trace has occurred

Install dev-master (e.g. from composer) and see if the issue is still there.

If the issue continues to happen on dev-master, then check for similar issues, and file an new issue (With stack trace and any information needed to reproduce, such as code snippets and related config settings) or comment on the similar issue.

### One of Phan's function or method signatures have incorrect parameter types or return types

Double check the documentation and examples provided on php.net (or for the latest stable version of that extension).

If Phan is inconsistent with the documentation, create a PR modifying the related functions/methods in https://github.com/phan/phan/blob/master/src/Phan/Language/Internal/FunctionSignatureMap.php (or file an issue).

### PHP 7.1 features such as nullable types aren't being parsed

Make sure that Phan is being invoked with php 7.1 (or newer). For best results, the PHP version should be close to the version of the project being analyzed.

The PHP version used to invoke Phan must be 7.1 or newer to parse php 7.1 code with `php-ast` (As well as for getting the real function and method signatures, etc.). `php-ast` uses the current PHP version's internal parse tree.

### A variadic function with phpdoc has unexpected types

The union type Phan reads for `@param` before `...` is the union type of individual elements that are passed by the caller, not the type within the function body. The below is an example of how a function should be documented.

```php
/** @param string ...$args (should be string, not string[] or array) */
function my_function(string ...$args) {}
my_function('arg1', 'other_arg');
```

See https://github.com/phpDocumentor/ReflectionDocBlock/blob/14f9edf1ae14d6ce417afb05a9ed37d7b3cc341e/tests/unit/DocBlock/Tags/ParamTest.php#L152-L168 - the phpdocumentor2 product does the same thing. It parses the individual element types as `string` from `@param string ...$varName`

### There are Different Issue Sets On Different Numbers of CPUs

See [[Different Issue Sets On Different Numbers of CPUs]]
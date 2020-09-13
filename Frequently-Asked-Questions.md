## Table of Contents

- [Phan says that an internal class, constant, or function (e.g. xdebug_is_enabled, Soap, memcached, etc.) is undeclared](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#phan-says-that-an-internal-class-constant-or-function-eg-xdebug_is_enabled-soap-memcached-etc-is-undeclared)
- [Phan says that a composer dependency of my project(a class, constant, or function) is undeclared](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#phan-says-that-a-composer-dependency-of-my-projecta-class-constant-or-function-is-undeclared)
- [Phan isn't picking up my project's doc comments](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#phan-isnt-picking-up-my-projects-doc-comments)
- [One of Phan's function or method signatures have incorrect parameter types or return types](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#one-of-phans-function-or-method-signatures-have-incorrect-parameter-types-or-return-types)
- [PHP 7.1 features such as nullable types aren't being parsed](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#php-71-features-such-as-nullable-types-arent-being-parsed)
- [Phan is warning about the code using PHP 7.1/7.2 features](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#phan-is-warning-about-the-codebase-using-syntax-that-is-incompatible-with-php-707172)
- [A variadic function with phpdoc has unexpected types](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#a-variadic-function-with-phpdoc-has-unexpected-types)
- [What should I do about `PhanRedefinedClassReference` issues emitted in vendor or third party code?](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#what-should-i-do-about-phanredefinedclassreference-issues-emitted-in-vendor-or-third-party-code)
- [I upgraded php-ast to 1.x and Phan no longer works](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#i-upgraded-php-ast-to-100-and-phan-no-longer-works)

- There are [[Different Issue Sets On Different Numbers of CPUs]]
- **[How to file a bug report for a crash, error, or incorrect analysis](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#how-to-file-a-bug-report-for-a-crash-error-or-incorrect-analysis)**

## Common Questions

<a name="undeclared_element"></a>
### Phan says that an internal class, constant, or function (e.g. xdebug_is_enabled, Soap, Memcached, etc.) is undeclared

For the best results, run Phan with the same extensions (and same extension versions) installed and enabled as you would in the project being enabled.

- This allows Phan to catch issues where you're using functionality that's not available in that project's target environment.

Phan supports [internal stubs](https://github.com/phan/phan/wiki/How-To-Use-Stubs#internal-stubs). [Regular stubs](https://github.com/phan/phan/wiki/How-To-Use-Stubs#stubs) are another option for older Phan releases.

Phan automatically disables xdebug for performance reasons. If your project uses xdebug, enable the corresponding [internal stubs](https://github.com/phan/phan/wiki/How-To-Use-Stubs#internal-stubs) in your project's `.phan/config.php`.

For historic reasons, Phan doesn't warn about global functions it has signatures for. [`ignore_undeclared_functions_with_known_signatures`](https://github.com/phan/phan/wiki/Phan-Config-Settings#ignore_undeclared_functions_with_known_signatures) is `true` by default, but can be set to `false` to also warn about undeclared global functions with known signatures.

### Phan says that a composer dependency of my project(a class, constant, or function) is undeclared

- You must add the directory (or file) of that dependency to `'directory_list'` or `'file_list'` in `.phan/config.php`. Usually, you will want to add `'vendor'` to `'exclude_analysis_directory_list'` as well. See https://github.com/phan/phan/wiki/Getting-Started#creating-a-config-file
- Make sure that the file exists in vendor/ (e.g. make sure that `composer.phar install` was executed if this is running in CI)
- Make sure that there are no typos in the variable name, that the namespace of the class is correct, and that the file containing the class/constant/function exists.

This is a common cause of PhanUndeclaredClassMethod, PhanUndeclaredClass, PhanUndeclaredFunction, etc.

### Phan isn't picking up my project's doc comments

See https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#doc-blocks (Common issues: Phan does not support inline doc comments, doc comments must begin with `/**`, etc)

You can enable [`PHPDocInWrongCommentPlugin`](https://github.com/phan/phan/tree/master/.phan/plugins#phpdocinwrongcommentplugin) to automatically warn about using block comments with annotations instead of doc comments.

### One of Phan's function or method signatures have incorrect parameter types or return types

Double check the documentation and examples provided on php.net (or for the latest stable version of that extension).

If Phan is inconsistent with the documentation, create a PR modifying the related functions/methods in https://github.com/phan/phan/blob/master/src/Phan/Language/Internal/FunctionSignatureMap.php (or file an issue).

### Phan is warning about the codebase using syntax that is incompatible with php 7.0 – 8.0

This can be solved by setting the `minimum_target_php_version` and `target_php_version` in your `.phan/config.php` to `'7.1'`/`'7.2'`/`'7.3'`/`'7.4'`/`'8.0'` (if that is the oldest php version your project supports), or by changing the code to stop using newer syntax. You may also suppress that issue in .phan/config.php, and various other ways.

+ `CompatibleNullableTypePHP70`, `CompatibleShortArrayAssignPHP70`, `CompatibleKeyedArrayAssignPHP70`,
  `CompatibleKeyedArrayAssignPHP70`, and `CompatibleIterableTypePHP70` (etc.)
  are emitted when the `minimum_target_php_version` is less than '7.1'.
  (In older Phan releases, `target_php_version` was used)
+ `CompatibleObjectTypePHP71` (etc.) are emitted for the `object` typehint when the `minimum_target_php_version`
  is less than 7.2.

Phan's latest releases attempt to automatically infer the `minimum_target_php_version` from the PHP version supported by your project's `composer.json`.
If Phan is warning, it's possible that your project may actually be used with a php version that's too old to support that functionality.

### A variadic function with phpdoc has unexpected types

The union type Phan reads for `@param` before `...` is the union type of individual elements that are passed by the caller, not the type within the function body. The below is an example of how a function should be documented.

```php
/**
 * @param string ...$args (should be string, not string[] or array,
 *                        and include "..." before the parameter name)
 */
function my_function(string ...$args) {}
my_function('arg1', 'other_arg');
```

See [the phpdocumentor2 implementation](https://github.com/phpDocumentor/ReflectionDocBlock/blob/14f9edf1ae14d6ce417afb05a9ed37d7b3cc341e/tests/unit/DocBlock/Tags/ParamTest.php#L152-L168) It parses the individual element types as `string` from `@param string ...$varName`. This detail of phpDocumentor2 is also documented in [the phpDocumentor/fig-standards repo](https://github.com/phpDocumentor/fig-standards/issues/40#issuecomment-138117263).

`@phan-param` can be used if you must use a different standard for documenting variadic `$args` with `@param`.

### What should I do about `PhanRedefinedClassReference` issues emitted in vendor or third party code?

`PhanRedefinedClassReference` is meant to draw attention to the fact duplicate class definitions in vendor, frameworks, etc are a common cause of confusing Phan errors in a project.

Possible solutions to `PhanRedefinedClassReference`:

1. I'd suggest just suppressing the issue entirely in your project in `suppress_issue_list` or affected locations if it isn't useful to you (e.g. you don't see mysterious issues involving that class)
2. Alternately, you could add the file declaring the class to exclude_file_list and add a stub file with just one of the class definitions to your file_list or directory_list. Keep in mind that this approach may cause false negatives/positives if the actual class definition changes in future releases.

This issue type was added to Phan because it can save a lot of time debugging for experienced or new Phan users for false positives in the code **outside of vendor** caused by the redefined class. (e.g. confusing warnings about incompatible types, missing methods/properties/class constants, failing to infer that ancestor class methods exist because Phan's using the first definition of a class/trait/interface it parses, which refers to a missing ancestor, etc.)

### I upgraded php-ast to 1.x and Phan no longer works.

Check the Phan version in your composer.json (or the method you're using to install Phan), e.g. with `/path/to/phan --version`.

You're likely still using Phan 0.12.x or 1.x or 2.x. You should upgrade to Phan 3.0.0+, php 7.2+, and php-ast 1.0.10+. (If you still need to use PHP 7.0 to execute Phan, upgrade to Phan 1.1.0+ and you'll be able to use php-ast 1.0.0+. If you need to use PHP 7.1, use Phan 2.x.).

The latest stable version of Phan is [![the Latest Stable Version](https://img.shields.io/packagist/v/phan/phan.svg)](https://packagist.org/packages/phan/phan)

### There are Different Issue Sets On Different Numbers of CPUs

See [[Different Issue Sets On Different Numbers of CPUs]]

### How to file a bug report for a crash, error, or incorrect analysis

Install `dev-master` of Phan (e.g. from composer) and see if the issue still occurs. It may have been fixed recently.

If the issue continues to happen on `dev-master`, then:

1. Check for similar issues. If there is anything new to add, add that.
2. If there are no similar issues, then file a new issue with any of the relevant information:
   - stack trace for a crash
   - Code snippets (or a link to an affected project, if possible) and config settings (if any) needed to reproduce the issue.
   - Phan version used.
   - Observed behavior and expected behavior.

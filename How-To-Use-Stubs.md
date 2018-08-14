From time to time you may encounter situations where there are entities that are used throughout a code base that Phan doesn't have access to. This may happen if your production environment uses a PHP extension that Phan doesn't have access to.

# Stubs

These are regular PHP files for Phan to parse but not analyze. They can be used for the following purposes:

- Stubbing third party PHP libraries that Phan can't analyze, or which have incorrect PHP doc
- Stubbing PHP modules that are unavailable, such as xdebug (However, [Internal Stubs](https://github.com/phan/phan/wiki/How-To-Use-Stubs#internal-stubs) may be a better approach)

Creating stubs that Phan has access to is pretty straightforward.

1. Create a directory `.phan/stubs` ([like Phan's](https://github.com/phan/phan/tree/master/.phan/stubs)).
2. Put code in there that stubs the constants/classes/properties/methods/functions of interest.
3. Reference the `.phan/stubs` directory from within `.phan/config.php` under `directory_list` ([like Phan's](https://github.com/phan/phan/blob/0655d1ed47e776ab281b91fd3ad0a9835e03b75a/.phan/config.php#L221)).

If you are using the stubs as a replacement for all/part of a third party library in directory_list, also add the replaced file(s) to `exclude_file_list` or `exclude_file_regex`.

JetBrains makes a very large number of stubs available at [github.com/JetBrains/phpstorm-stubs](https://github.com/JetBrains/phpstorm-stubs/tree/master/standard). You may wish to consider using some of these for classes you need access to.

# Internal Stubs

Internal stubs can be declared with 'autoload_internal_extension_signatures' => [...]`) in your `.phan/config.php`. That setting will point to the parsable PHP files that will be used
if (and only if) the corresponding PHP extension(i.e. module) (xdebug, memcached, etc.) isn't available in the PHP binary used to run Phan.

- Phan will use the real signature (properties, constants, methods, param counts, return types and *real* param types (Frequently empty for modules), etc.) from the `.phan_php` file instead of what it would have retrieved via PHP's Reflection APIs.
- When using `autoload_internal_extension_signatures`, Phan will act almost identically to how it would behave if the extension were installed and enabled.
  Phan will use it's own information about what the internal functions, classes, and methods **should** have as union types (for params, return types, properties, etc.).
  (Phan will also emit the same issue types, emitting `PhanParamTooManyInternal` instead of `PhanParamTooMany`, etc.)

A common use case is to have Phan analyze a codebase as if various extensions are present (e.g. `xdebug`). The stubs contain almost the same info as the real extensions would: The real (Reflection) class, function, and constant signatures.

## Obtaining Internal Stubs
### Generating Stubs

Generating stubs yourself is probably the most reliable way.
With a php binary that has the required extension installed and enabled: (Preferably with the same extension version and configuration you're using)

```
EXTENSION_NAME=xdebug
vendor/phan/phan/tool/make_stubs -e $EXTENSION_NAME | tee $EXTENSION_NAME.phan_php
```

`tool/make_stubs` may be refactored into a standalone script in a future phan release.

### Downloading Internal Stubs

Phan is bundled with some internal stubs, but *only for extensions it uses for self-analysis*. See https://github.com/phan/phan/tree/master/.phan/internal_stubs

https://github.com/TysonAndre/phan_stubs/tree/master/stubs contains a small number of stubs for extensions (for php 7.1, most should work with 7.0) that are internal or external to php. This may increase in the future.  **If you wish to contribute stubs, do so there**. This repo may be moved to the phan organization later on.

You may or may not have success with stubs from [github.com/JetBrains/phpstorm-stubs](https://github.com/JetBrains/phpstorm-stubs/tree/master/standard), the phpdoc may change Phan's behavior.

## Using Internal Stubs in a project

Add a `'autoload_internal_extension_signatures' => ['extension_name' => 'path/to/stubs/for/extension_name.phan_php']` entry to your .phan/config.php

```php
    // You can put relative paths to internal stubs in this config option.
    // Phan will continue using its detailed type annotations,
    // but load the constants, classes, functions, and classes (and their Reflection types)
    // from these stub files (doubling as valid php files).
    // Use a different extension from php (and preferably a separate folder)
    // to avoid accidentally parsing these as PHP (includes projects depending on this).
    // The 'mkstubs' script can be used to generate your own stubs (compatible with php 7.0+ right now)
    // Note: The array key must be the same as the extension name reported by `php -m`,
    // so that phan can skip loading the stubs if the extension is actually available.
    'autoload_internal_extension_signatures' => [
         // Xdebug stubs are bundled with Phan 0.10.1+/0.8.9+ for usage,
         // because Phan disables xdebug by default.
        'xdebug'     => 'vendor/phan/phan/.phan/internal_stubs/xdebug.phan_php',
        'memcached'  => '.phan/your_internal_stubs_folder_name/memcached.phan_php',
    ],
```

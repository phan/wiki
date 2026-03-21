# Getting Started with Phan

Phan v6 is the current release and is recommended for PHP 8.1+ codebases. This guide covers setting up Phan v6.

**For legacy PHP projects**: If you need to analyze code written for PHP 8.0 or earlier, see [[Migrating-to-Phan-V6]] for information about using Phan v5.

## Quick Start

To get started, choose [a method for getting Phan running on your system](#installing-phan) and then [create a config file](#creating-a-config-file) so that Phan knows where to look for code and how to analyze it.

If you're having trouble getting Phan up and running, [open an issue on GitHub](https://github.com/phan/phan/issues) or [join the Gitter chat](https://gitter.im/phan/phan).
If Phan is running but you're unsure why it's emitting certain issues, see [[Frequently Asked Questions]].

[![Gitter](https://badges.gitter.im/phan/phan.svg)](https://gitter.im/phan/phan?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

Maintainers should be able to respond fairly quickly before/after US east coast working hours.

# Installing Phan


## Installing Dependencies

**Phan v6** requires:
- **PHP 8.1 or later** to run Phan itself
- **php-ast 1.1.3+** for analyzing PHP 8.4+ code
- **php-ast 1.0.11+** for analyzing earlier PHP versions

The [[Phan-Helpers-Extension]] (optional) can provide 2-3x performance improvements for large projects.

To compile [php-ast](https://github.com/nikic/php-ast): Something along these lines should do it ([Alternate instructions](https://github.com/nikic/php-ast#installation)):

```sh
pecl install ast-1.0.14
```

And add `extension=ast.so` to your `php.ini` file. Check that it is there with `php -m`.
If it isn't, then you probably added it to the wrong `php.ini` file.
Check `php --ini` to see where it is looking.

If `phpize` is unavailable on your system, you may need to install the PHP developer
packages which are often available with names such as `php-dev`.

Windows users can grab `ast.dll` directly from [PECL releases](https://windows.php.net/downloads/pecl/releases/ast/).

## Composer

If you're managing dependencies via [Composer](https://getcomposer.org/), you can add Phan v6 to your project by running:

```sh
composer require --dev "phan/phan:^6.0"
```

[For a full list of releases, check out https://packagist.org/packages/phan/phan](https://packagist.org/packages/phan/phan).

With the Phan dependency installed, you can do analysis by running the following (once you create a configuration file).

```sh
./vendor/bin/phan
```

## From Source

Before you begin, make sure you have:
- **PHP 8.1+** installed
- The [php-ast module](https://github.com/nikic/php-ast) installed (1.1.3+ for PHP 8.4 support)

Then clone the Phan source and use composer to install dependencies:

```sh
git clone https://github.com/phan/phan.git
cd phan
composer install
```

Verify Phan is working correctly:

```sh
./phan --version
./vendor/bin/phpunit
```

Run Phan on itself (using its own [`.phan/config.php`](https://github.com/phan/phan/blob/master/.phan/config.php) configuration):

```sh
./phan
```

## From Phan.phar

To run Phan from a Phar package, you can download the latest Phar and run it.

```sh
curl -L https://github.com/phan/phan/releases/latest/download/phan.phar -o phan.phar
```

You'll now be able to run Phan via

```sh
php phan.phar
```

## From Docker Image

The official Phan Docker image includes all dependencies (PHP 8.1+, php-ast):

```sh
docker run -v $(pwd):/app etsy/phan:latest phan
```

For more information, see the [official Phan Docker repository](https://github.com/phan/docker).

Other options:
* [jakzal/phpqa](https://github.com/jakzal/phpqa) - includes multiple PHP analysis tools
* [Search for other Docker images](https://www.google.com/webhp#q=phan%20docker)

## From Homebrew

See [[Getting Started With Homebrew]] (useful for Mac OS)

# Creating a Config File

You'll want to create a configuration file within your codebase at `.phan/config.php` so that Phan knows what to look at. The following config file will let Phan know that your source is in the `src/` with some dependency code in the `vendor/symfony/console` directory and that anything under `vendor/` should be parsed, but not analyzed.

```php
<?php

/**
 * This configuration will be read and overlaid on top of the
 * default configuration. Command-line arguments will be applied
 * after this file is read.
 */
return [
    // Target PHP version(s) for analysis.
    // Supported values: '8.1', '8.2', '8.3', '8.4', '8.5', null
    //
    // If null, Phan uses the PHP version that is running Phan itself.
    // Minimum version is PHP 8.1 for target analysis.
    'target_php_version' => '8.1',

    // A list of directories that should be parsed for class and
    // method information. After excluding the directories
    // defined in exclude_analysis_directory_list, the remaining
    // files will be statically analyzed for errors.
    //
    // Thus, both first-party and third-party code being used by
    // your application should be included in this list.
    'directory_list' => [
        'src',
        'vendor/symfony/console',
    ],

    // A regex used to match every file name that you want to
    // exclude from parsing. Actual value will exclude every
    // "test", "tests", "Test" and "Tests" folders found in
    // "vendor/" directory.
    'exclude_file_regex' => '@^vendor/.*/(tests?|Tests?)/@',

    // A directory list that defines files that will be excluded
    // from static analysis, but whose class and method
    // information should be included.
    //
    // Generally, you'll want to include the directories for
    // third-party code (such as "vendor/") in this list.
    //
    // n.b.: If you'd like to parse but not analyze 3rd
    //       party code, directories containing that code
    //       should be added to both the `directory_list`
    //       and `exclude_analysis_directory_list` arrays.
    'exclude_analysis_directory_list' => [
        'vendor/'
    ],
];
```

### Auto-Generating Configuration

You can auto-generate a `.phan/config.php` using the `--init` command:

```bash
phan --init --init-level=3
```

The `--init-level` parameter controls strictness:
- `--init-level=1`: Very strict analysis (many config options enabled)
- `--init-level=3`: Recommended starting point (good balance)
- `--init-level=5`: Lenient analysis (minimal issues reported)

You may need to manually add some indirect third-party dependencies to `directory_list`.

### Configuration References

- See [[Incrementally Strengthening Analysis]] for tips on gradually increasing analysis strictness
- Full option reference: [[Phan Config Settings]]
- Example: Phan's own [`.phan/config.php`](https://github.com/phan/phan/blob/master/.phan/config.php) shows a strict production configuration

# Running Phan in CI/CD Pipelines

Assuming you have set up `.phan/config.php` in your project root, you can integrate Phan into your CI/CD pipeline.

Phan exits with:
- `0` - No issues found
- `1` - Issues were detected
- `2` - Fatal error during analysis

Most CI tools can detect these exit codes to pass or fail the build.

## Example CI/CD Configurations

### GitHub Actions

```yaml
name: Phan Analysis

on: [push, pull_request]

jobs:
  phan:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          extensions: ast
      - run: composer install --dev
      - run: vendor/bin/phan
```

### GitLab CI

```yaml
phan:
  image: php:8.1
  before_script:
    - apt-get update && apt-get install -y php-dev
    - pecl install ast-1.1.3
    - composer install --dev
  script:
    - vendor/bin/phan
```

### Generic (Travis, Circle CI, etc.)

```bash
composer install --dev
vendor/bin/phan --output-mode=checkstyle > phan-report.xml
```

See [[Using-Phan-From-Command-Line]] for more CLI options, including `--output-mode=json` or `--output-file`.

# Try Phan Online

You can try Phan entirely in your browser with the [WebAssembly demo](https://phan.github.io/demo/) - no installation needed!

This is a great way to explore Phan's features before setting it up on your project.

Works best on desktop Firefox or Chrome.

[![Phan demo](https://raw.githubusercontent.com/phan/demo/master/static/preview.png)](https://phan.github.io/demo/)

# Next Steps

Once you have Phan installed and running:

1. **Learn annotation syntax**: [[Annotating-Your-Source-Code-V6]] - How to add type hints to improve analysis
2. **Understand issue types**: [[Issue-Types-Reference]] - Reference for all issue types Phan can detect
3. **Improve analysis gradually**: [[Incrementally Strengthening Analysis]] - Increase strictness over time
4. **Set up fast re-analysis**: [[Incremental-Analysis]] - Speed up analysis on code changes
5. **Optimize for large projects**: [[Memory-and-Performance-Optimizations]] and [[Phan-Helpers-Extension]]

# Upgrading from Phan v5

If you're upgrading from Phan v5, see [[Migrating-to-Phan-V6]] for:
- Breaking changes to be aware of
- Migration steps
- Configuration changes needed

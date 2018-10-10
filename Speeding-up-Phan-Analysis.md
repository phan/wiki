The following suggestions may help speed up Phan analysis on your project:

1. [Make sure nothing is slowing down the PHP interpreter.](#1-php-configuration-options) (e.g. xdebug)

2. [Avoid using known slow Phan configuration operations for CI for large projects](#2-avoid-using-slow-phan-configuration-options) (and projects with large vendor directories). You can periodically run dead code checks, etc. manually.

3. [Reduce the number of files phan parses.](#3-reduce-the-number-of-files-phan-parses) Phan ignores any PHP autoloading configuration that is set up. It unconditionally parses all files and directories listed in the parsed file and directory list.

4. [Start using multiple processes for Phan analysis.](#4-start-using-multiple-processes-for-phan-analysis)

5. [Run Phan only on the files you changed](#5-run-phan-only-on-the-files-you-changed) (Daemon mode or --include-analysis-file-list). This may be useful during local development, but not for Continuous Integration.

6. [Run the latest release of Phan.](#6-run-the-latest-release-of-phan)

7. [Install an optional C module.](#7-install-an-optional-c-module-for-php--71) (PHP <= 7.1 only)

## 1. PHP Interpreter Configuration Settings

1. Disable xdebug before running Phan (Phan runs around 5 times slower with xdebug enabled)
   In Phan 0.10.1+/0.8.9+, Phan disables XDebug automatically.

   The output of `php -m` will mention XDebug if XDebug is still enabled.

   XDebug may be disabled in various ways, e.g. by setting the environment variable `PHPRC` to the absolute path to a folder containing a copy of php.ini without XDebug.

   (XDebug is only suggested if you're investigating a crash, or a test failure in Phan's test suite, or an uncaught error in Phan, and shouldn't be used in normal usage.)
2. Run `php --version`. Normally, you should see (NTS), not (ZTS DEBUG) or (NTS DEBUG), but if you build PHP yourself (e.g. for PECL module development), the version you're currently using may have been built with `--enable-debug` (DEBUG).
   php built with `--enable-debug` (DEBUG) is around 2 times slower.

   Also, Phan runs the fastest in php 7.2+.

## 2. Avoid Using Slow Phan Configuration Options

These configuration options should be changed to the below values in your project's `.phan/config.php`. (For the settings with a value of false, omitting them would do the same thing)

```php
    // Backwards Compatibility Checking
    // (Disable this if the application no longer supports php 5,
    // or use a different tool.
    // Phan's checks are currently slow)
    // Set it to false or omit it.
    'backward_compatibility_checks' => false,

    // A regular expression to match files to be excluded
    // from parsing and analysis and will not be read at all.
    //
    // This is useful for excluding groups of test or example
    // directories/files, unanalyzable files, or files that
    // can't be removed for whatever reason.
    // (e.g. '@Test\.php$@', or '@vendor/.*/(tests|Tests)/@')
    'exclude_file_regex' => '@^vendor/.*/(tests|Tests)/@',

    // Disabling this may give a small performance boost.
    'simplify_ast' => false,

    // This is somewhat slow and doesn't work with multiple processes.
    // Set it to false or omit it.
    'dead_code_detection' => false,
```

### 3. Reduce the Number of Files Phan Parses

Phan currently ignores any autoloading configuration that is set up for your project.
It assumes that the files will be autoloaded correctly,
and will unconditionally parse all files and directories listed in the parsed file and directory list (except for the excluded ones).


For example, if your project's `.phan/config` has config options similar to the below settings:

```php
    // An example of a config which parses too many files:
    'directory_list' => [
        'src',
        // This includes more files than needed,
        // and tests may have high false positives rates
        'tests',
        // This includes more files than needed.
        // Including only the direct dependencies is recommended.
        'vendor',
        'path/to/NonComposerThirdPartyProjects',
    ],
    "exclude_analysis_directory_list" => [
        'vendor/'
        'tests/'
        'path/to/NonComposerThirdPartyProjects',
    ],
```

Then Phan analysis would complete faster if you change it to the below (may require some tweaking to add any classes that are indirect dependencies):

```php
    // Be as specific as possible with 'directory_list' and 'file_list'
    // to avoid parsing files (or scanning directories) unnecessarily.
    'directory_list' => [
        'src',
        // If the tests pass, you may not need to run Phan on your unit tests.
        // However, some files may still need to be included,
        // e.g. for constant definitions,
        // defining classes used by `instanceof` checks, etc.
        'tests/ClassesUsedBySrc/',
        // You can parse fewer files if you list only direct dependencies
        // (exclude as many devDependencies as possible).
        // (And maybe dependencies of those dependencies, to fix any Phan issues that show up.)
        // Additionally, it's faster to avoid parsing tests, examples, etc.
        'vendorName/directDependencyProject1/src',
        'vendorName/directDependencyProject2/library',
        'vendorName/directDependencyProject3/src/ProjectName',
        'path/to/NonComposerThirdPartyProjects/project1/src',
        'path/to/NonComposerThirdPartyProjects/project2/src',
    ],
    // No change here
    "exclude_analysis_directory_list" => [
        'vendor/'
        'tests/'
        'path/to/NonComposerThirdPartyProjects',
    ],

    // A regular expression to match files to be excluded
    // from parsing and analysis and will not be read at all.
    //
    // This is useful for excluding groups of test or example
    // directories/files, unanalyzable files, or files that
    // can't be removed for whatever reason.
    // (e.g. '@Test\.php$@', or '@vendor/.*/(tests|Tests)/@')
    'exclude_file_regex' => '@^(vendor|path/to/thirdparty)/.*/(tests|Tests|doc|examples)/@',
```

### 4. Start Using Multiple Processes for Phan Analysis

The analysis phase generally takes much longer than the parse phase.
Note that this will significantly increase the total amount of memory Phan will use (Watch the memory usage with a tool such as `htop` (unix/linux)).

If you're planning to start using multiple processes and haven't done so before, it's a good idea to read [[Different Issue Sets On Different Numbers of CPUs]] and [what to do about different issue sets](https://github.com/phan/phan/wiki/Different-Issue-Sets-On-Different-Numbers-of-CPUs#what-to-do-about-it)

### 5. Run Phan Only on the Files You Changed

This will be less accurate, but much faster (Won't be able to infer types of unspecified parameters from calls made from outside of the function).

There are two ways to do this.

1. By [[Using Phan Daemon Mode]] (Experimental), which lets you reuse the results of the parse phase
2. By invoking Phan with `--include-analysis-file-list`

```
 --include-analysis-file-list <file_list>
  A comma-separated list of files that will be included in
  static analysis. All others won't be analyzed.

  This is primarily intended for performing standalone
  incremental analysis.
```

### 6. Run the Latest Release of Phan

The latest release (for php 7.2) is [![the Latest Stable Version](https://img.shields.io/packagist/v/phan/phan.svg)](https://packagist.org/packages/phan/phan)

### 7. Install an optional C module (for PHP <= 7.1)

Phan started calling `spl_object_id(object $object) : int` in recent Phan releases. `spl_object_id()` is built into PHP 7.2+. If you are running php <= 7.1, `spl_object_id()` is also provided by https://github.com/runkit7/runkit_object_id.

Earlier releases of Phan can be sped up around 10% by installing a native C implementation of spl_object_id/runkit_object_id. See https://github.com/phan/phan/pull/729

- This performance increase won't be as large in the latest releases, which use spl_object_id less frequently (in different parts of the code base)

- In PHP <= 7.1, https://github.com/runkit7/runkit_object_id is recommended (It also provides `spl_object_id()` for PHP <= 7.1 by default.)
- https://github.com/runkit7/runkit7 (PHP7 port of https://secure.php.net/runkit) includes `runkit_object_id`, but is not recommended unless it is already installed. It implements other functions that aren't needed for Phan, and those functions make PHP code harder to reason about. The `runkit_object_id` extension also provides a native implementation of `spl_object_id` for PHP <= 7.1, which future Phan releases will use (disabled by default in runkit7/runkit7).

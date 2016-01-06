A large mature PHP code base is very unlikely to pass analysis cleanly. This tutorial is meant to provide some guidance on how to get started analyzing your code and get to a place where static analysis is actually useful.

# Getting Started

The process of starting analysis looks like

* Get Phan running
* Set up your environment
* Start doing the weakest analysis possible and fixing annotations
* Slowly ramp-up the strength of the analysis

## Get Phan Running

The first step is to get Phan running. Take a look at [the README](https://github.com/etsy/phan#getting-it-running) for help getting PHP7, the php-ast module and Phan installed.

If you have PHP version 7 installed with the [php-ast](https://github.com/nikic/php-ast) module and your project uses composer, you can just run `composer require --dev "etsy/phan:dev-master"` to add Phan to your project with the binary available in `vendor/bin/phan`.

## Set Up Your Environment

Because many people will be running Phan over and over, it'll be helpful to check in a Phan config to the root of your code base.

To do this, create a directory `.phan` in the root of your project and make a file `.phan/config.php` with the following contents.

```php
<?php

use \Phan\Config;

/**
 * This configuration will be read and overlayed on top of the
 * default configuration. Command line arguments will be applied
 * after this file is read.
 *
 * @see src/Phan/Config.php
 * See Config for all configurable options.
 *
 * A Note About Paths
 * ==================
 *
 * Files referenced from this file should be defined as
 *
 * ```
 *   Config::projectPath('relative_path/to/file')
 * ```
 *
 * where the relative path is relative to the root of the
 * project which is defined as either the working directory
 * of the phan executable or a path passed in via the CLI
 * '-d' flag.
 */
return [
    // If true, missing properties will be created when
    // they are first seen. If false, we'll report an
    // error message.
    "allow_missing_properties" => true,

    // Allow null to be cast as any type and for any
    // type to be cast to null.
    "null_casts_as_any_type" => true,

    // Backwards Compatibility Checking
    'backward_compatibility_checks' => false,

    // Run a quick version of checks that takes less
    // time
    "quick_mode" => true,

    // Only emit critical issues
    "minimum_severity" => 10,

    // A set of fully qualified class-names for which
    // a call to parent::__construct() is required
    'parent_constructor_required' => [
    ],

    // A list of directories holding code that we want
    // to parse, but not analyze
    "exclude_analysis_directory_list" => [
    ],
];
```

This configuration sets up the weakest possible analysis to you get started. With this configuration we allow undefined properties to be written to, allow things of type `null` to be cast to any type, and only emit the most severe issues.

You can take a look at [Phan's config](https://github.com/etsy/phan/blob/master/.phan/config.php) for an example.
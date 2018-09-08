A large mature PHP code base is very unlikely to pass analysis cleanly. This tutorial is meant to provide some guidance on how to get started analyzing your code and get to a place where static analysis is actually useful.

Edits to this page are very welcome. Feel free to get in touch if you have any questions.

[![Gitter](https://badges.gitter.im/phan/phan.svg)](https://gitter.im/phan/phan?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

# Getting Started

The process of starting analysis looks like

* Get Phan running
* Set up your environment
* Generate a File List
* Start doing the weakest analysis possible and fixing false-positives
* Enforce clean analysis for your team
* Slowly ramp-up the strength of the analysis

## Get Phan Running

The first step is to get Phan running. Take a look at [Getting Started](https://github.com/phan/phan/wiki/Getting-Started) for help getting PHP7, the php-ast module and Phan installed.

If you have PHP version 7 installed with the [php-ast](https://github.com/nikic/php-ast) module and your project uses composer, you can just run `composer require --dev "phan/phan:dev-master"` to add Phan to your project with the binary available in `vendor/bin/phan`.

## Set Up Your Environment

Because many people will be running Phan over and over, it'll be helpful to check in a Phan config to the root of your code base.

There are three ways to set this up

### 1. Automated setup for composer projects

This can be used on a project that has composer.json set up with an autoloader configuration for your project. If your project does not use composer, [manually set up `.phan/config.php` instead](https://github.com/phan/phan/wiki/Tutorial-for-Analyzing-a-Large-Sloppy-Code-Base/#2-manual-setup)

```sh
cd /path/to/project-to-analyze
# --init-level can be anywhere from 1 (strictest) to 5 (least strict)
path/to/phan --init --init-level=5
```

We recommend manually checking the generated config to see if the directory_list, file_list, and exclusions are reasonable.

If Phan runs report that classes/global functions/global constants are missing, you will probably need to [add the corresponding folder to the directory_list of `.phan/config.php`](https://github.com/phan/phan/wiki/Frequently-Asked-Questions#phan-says-that-a-composer-dependency-of-my-projecta-class-constant-or-function-is-undeclared)

### 2. Manual Setup

To manually set up a .phan/config.php instead, create a directory `.phan` in the root of your project and make a file `.phan/config.php` with the following contents:

```php
<?php

use Phan\Config;

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

    // Only emit critical issues to start with
    // (0 is low severity, 5 is normal severity, 10 is critical)
    "minimum_severity" => 10,

    // A list of directories that should be parsed for class and
    // method information. After excluding the directories
    // defined in exclude_analysis_directory_list, the remaining
    // files will be statically analyzed for errors.
    //
    // Thus, both first-party and third-party code being used by
    // your application should be included in this list.
    'directory_list' => [
         // Change this to include the folders you wish to analyze
         // (and the folders of their dependencies)
         'src',
         // To speed up analysis, we recommend going back later and
         // limiting this to only the vendor/ subdirectories your
         // project depends on.
         // `phan --init` will generate a list of folders for you
         'vendor',
    ],

    // A list of directories holding code that we want
    // to parse, but not analyze
    "exclude_analysis_directory_list" => [
         'vendor',
    ],
];
```

This configuration sets up the weakest possible analysis to get you started. With this configuration we allow undefined properties to be written to, allow things of type `null` to be cast to any type, and only emit the most severe issues.

You can take a look at [Phan's config](https://github.com/phan/phan/blob/master/.phan/config.php) for an example.

You should add any third-party code paths to the `exclude_analysis_directory_list` array so as to avoid having to deal with sloppiness in code that you don't want to fix.

### 3. Generate a File List

If the files and directories that you are going to analyze can't be represented in `.phan/config.php` (They usually can be), see [Externally generating a list of files for Phan](https://github.com/phan/phan/wiki/Externally-generating-a-list-of-files-for-Phan)

If you have saved a list of files to `./files`, then when running Phan, add the option `-f ./files` when invoking Phan so that it will read files to analyze from that list.

## Start Doing a Weak Analysis

After doing this, we recommend checking in `.phan/config.php` into source control.


With Phan installed and running and a configuration file, you can now do an analysis.

```sh
# add '-f files' if you chose to manually generate a file list
phan --progress-bar -o analysis.txt
```

If after running there are no errors in the file `analysis.txt`, either something went wrong or your code base is a magical unicorn.

Now that you have a giant list of severe issues for which the majority are likely false-positives, you're going to want to start fixing annotations and method signatures.

To find your largest sources of issues, its a good idea to look at your breakdown of issue types;

```sh
cat analysis.txt| cut -d ' ' -f2 | sort | uniq -c | sort -n -r
```

This should look something like

```
    546 PhanUndeclaredClassMethod
    118 PhanUndeclaredClassCatch
     98 PhanUndeclaredFunction
     77 PhanNonClassMethodCall
     59 PhanUndeclaredClassConstant
      7 PhanUndeclaredExtendedClass
      7 PhanContextNotObject
      5 PhanAccessPropertyPrivate
      3 PhanAccessPropertyProtected
      2 PhanParentlessClass
      1 PhanUndeclaredInterface
      1 PhanUndeclaredClassInstanceof
      1 PhanUndeclaredClass
```

You can now run the following to see your most common issues;

```sh
cat analysis.txt | grep PhanUndeclaredClassMethod
```

Some common sources of false-positives include

* Annotations (@param, @var, @return) that aren't formatted correctly (as per [phpDocumentor](http://www.phpdoc.org/)). You'll want to go through and fix the formatting so we're picking up the actual type being referenced.
* Sloppy annotations that don't accurately name the type being returned such as `@return Thing` when `Thing` isn't in any known namespace
* Third party code that wasn't included in the analysis but whose classes and methods are still being used.

Fixing all of this stuff isn't going to be fun. Go get a cup of coffee, clear your schedule and get cozy. Perhaps this is a good time to work from home for a few days in isolation.

## Enforce clean analysis for your team

Now that you have clean output, you're going to want to make sure it stays that way. Whatever your team's process is, you'll want to make sure folks can't push code that doesn't pass analysis. If you don't, you're going to find yourself feeling like you have to constantly clean up after folks, and nobody wants that.

- See [Running Phan in Continuous Integration](https://github.com/phan/phan/wiki/Getting-Started#running-phan-in-continuous-integration)

Also, if you haven't done so already, check `.phan/config.php` into source control.

## Slowly Ramp-Up the Strength of the Analysis

Take a look at [[Incrementally Strengthening Analysis]] for some ideas on how to configure the strength of your analysis.

Now that you've gotten rid of all of the false-positives and Phan is producing a clean set of issues that are truly problematic, you can start increasing Phan's strength until you get to a spot where you're comfortable blocking code that doesn't pass.

You'll want to consider

* Moving `minimum_severity` in your config from `10` (critical) to `5` (normal) or even `0` (low) if you love fixing bugs.
* Setting `backward_compatibility_checks` to `true` to find out how your PHP5 code is going to break in PHP7.
* Setting `null_casts_as_any_type` to `false` to find out what kind of madness your team has been up to.
* Setting `allow_missing_properties` to `false` to see all of the undefined properties you've been writing to all these years.
* Setting `quick_mode` to `false` to re-analyze each method on each method call given the input types you're sending it.
* [Adding some of Phan's plugins](https://github.com/phan/phan#features-provided-by-plugins) to your [`.phan/config.php`](https://github.com/phan/phan#usage)

Setting these values one at a time and fixing all issues you find will make the process less overwhelming.

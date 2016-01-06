A large mature PHP code base is very unlikely to pass analysis cleanly. This tutorial is meant to provide some guidance on how to get started analyzing your code and get to a place where static analysis is actually useful.

# Getting Started

The process of starting analysis looks like

* Get Phan running
* Set up your environment
* Generate a File List
* Start doing the weakest analysis possible and fixing false-positives
* Enforce clean analysis for your team
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

You should add any third-party code paths to the `exclude_analysis_directory_list` array so as to avoid having to deal with sloppiness in code that you don't want to fix.

## Generate a File List

You can pass the files to be analyzed to Phan on the command-line, but with a large code base, you'll want to create a file that lists all files and filters out junk to make your life easier.

One way to generate this file list would be to create a file `.phan/generate_file_list.sh` with the following contents.

```sh
#!/bin/bash

if [[ -z $WORKSPACE ]]
then
    export WORKSPACE=~/path/to/code/src
fi

cd $WORKSPACE

JUNK=/var/tmp/junk.txt

for dir in \
    src \
    vendor/path/to/project
do
    if [ -d "$dir" ]; then
        find $dir -name '*.php' >> $JUNK
    fi
done

cat $JUNK | \
    grep -v "junk_file.php" | \
    grep -v "junk/directory.php" | \
    awk '!x[$0]++'

rm $JUNK
```

You can then run `./.phan/generate_file_list.sh > files`. Take a look at [Phan's file list generator](https://github.com/etsy/phan/blob/master/.phan/generate_file_list.sh) to see an example.

With this, you can now run `phan -f files` to run an analysis of your code base.

## Start Doing a Weak Analysis

With Phan installed and running, a configuration file and a file list, you can now do an analysis.

```sh
phan --progress-bar -f files -o analysis.txt
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
* Third party code that wasn't included in the analysis but who's classes and methods are still being used.

Fixing all of this stuff isn't going to be fun. Go get a cup of coffee, clear your schedule and get cozy. Perhaps this is a good time to work from home for a few days in isolation.

## Enforce clean analysis for your team

Now that you have clean output, you're going to want to make sure it stays that way. Whatever your team's process is, you'll want to make sure folks can't push code that doesn't pass analysis. If you don't, you're going to find yourself feeling like you have to constantly clean up after folks, and nobody wants that.

## Slowly Ramp-Up the Strength of the Analysis

Now that you've gotten rid of all of the false-positives and Phan is producing a clean set of issues that are truly problematic, you can start increasing Phan's strength until you get to a spot where you're comfortable blocking code that doesn't pass.

You'll want to consider

* Moving `minimum_severity` in your config from `10` (severe) to `5` (normal) or even `0` (low) if you love fixing bugs
* Set `backward_compatibility_checks` to `true` to find out how your PHP5 code is going to break in PHP7.
* Set `null_casts_as_any_type` to false to find out what kind of madness your team has been up to.
* Set `allow_missing_properties` to `false` to see all of the undefined constants you've been writing too all these years
* Set `quick_mode` to `false` to re-analyze each method on each method call given the input types you're sending it.

Setting these values one at a time and fixing all issues you find will make the process less overwhelming.
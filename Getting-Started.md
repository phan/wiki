There are several options for getting Phan up and running on your code base.

To get started, choose [a method for getting Phan running on your system](https://github.com/phan/phan/wiki/Getting-Started#installing-phan) and then [create a config file](https://github.com/phan/phan/wiki/Getting-Started#creating-a-config-file) so that Phan knows where to look for code and how to analyze it. Take a look at [Installing Phan Dependencies](https://github.com/phan/phan/wiki/Getting-Started#installing-phan-dependencies) for some help getting Phan's dependencies running.

If you're having trouble getting Phan up and running, get in touch.
If it's running but you're not sure of why some issues emitted by Phan are there, see [[Frequently Asked Questions]].

[![Gitter](https://badges.gitter.im/phan/phan.svg)](https://gitter.im/phan/phan?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

Maintainers should be able to respond fairly quickly during US east coast/west coast working hours.

# Installing Phan


## Installing Dependencies

Phan depends on [PHP](http://php.net/) version 7 or greater and [php-ast](https://github.com/nikic/php-ast) 0.1.5+ by [Nikita Popov](https://github.com/nikic).
The latest Phan releases (>= 1.1.0) also support php-ast 1.0.0+.

If you don't have a version of PHP 7 installed, you can grab a php7dev Vagrant image or one of the many Docker builds out there.

To compile [php-ast](https://github.com/nikic/php-ast): Something along these lines should do it ([Alternate instructions](https://github.com/nikic/php-ast#installation)):

```sh
pecl install ast-0.1.6
```

And add `extension=ast.so` to your `php.ini` file. Check that it is there with `php -m`.
If it isn't, then you probably added it to the wrong `php.ini` file.
Check `php --ini` to see where it is looking.

If `phpize` is unavailable on your system, you may need to install the PHP developer
packages which are often available with names such as `php-dev`.

Windows users can grab `ast.dll` directly from [PECL snaps](http://windows.php.net/downloads/pecl/snaps/ast/)

## Composer

If you're managing dependencies via [Composer](https://getcomposer.org/), you can add Phan to your project by running the following.

```sh
composer require --dev "phan/phan:1.x"
```

[For a full list of releases, check out https://packagist.org/packages/phan/phan](https://packagist.org/packages/phan/phan).

With the Phan dependency installed, you can do an analysis by running the following (once you create a configuration file).

```sh
./vendor/bin/phan
```

## From Source

Before you begin, you'll want to make sure you have PHP version 7+ with the [php-ast module](https://github.com/nikic/php-ast) installed.

From there, you can clone the Phan source and use composer to install its dependencies.

```sh
git clone https://github.com/phan/phan.git
cd phan
composer install
```

You should now be able to run `./test` to make sure Phan is working correctly, and run `./phan` to run Phan on itself (using its own [`.phan/config.php`](https://github.com/phan/phan/blob/master/.phan/config.php) configuration).

## From Phan.phar

To run Phan from a Phar package, you can download the Phar and run it.

```sh
curl -L https://github.com/phan/phan/releases/download/1.1.2/phan.phar -o phan.phar;
```

You'll now be able to run Phan via

```sh
php phan.phar
```

When this was last updated, [1.1.2](https://github.com/phan/phan/releases/tag/1.1.2) was the latest release. You may wish to check [the list of releases](https://github.com/phan/phan/releases) to see if that's still the latest, as I'll probably forget to update this page with subsequent releases.

According to packagist, the latest stable version is [![the Latest Stable Version](https://img.shields.io/packagist/v/phan/phan.svg)](https://packagist.org/packages/phan/phan)

## From a Docker Image

Several docker images exist for Phan that include its dependencies (PHP version 7, php-ast). I haven't tried any of them myself, but you could check out these projects.

* https://github.com/cloudflare/docker-phan
* https://github.com/mre/docker-php-phan
* https://github.com/madmuffin1/phan-docker
* [Others that show up now and then](https://www.google.com/webhp#q=phan%20docker)

## From Homebrew

See [[Getting Started With Homebrew]]

# Creating a Config File

You'll want to create a configuration file within your code base at `.phan/config.php` so that Phan knows what to look at. The following config file will let Phan know that your source is in the `src/` with some dependency code in the `vendor/symfony/console` directory, and that anything under `vendor/` should be parsed, but not analyzed.

```php
<?php

/**
 * This configuration will be read and overlaid on top of the
 * default configuration. Command line arguments will be applied
 * after this file is read.
 */
return [
    // Supported values: '7.0', '7.1', '7.2', null.
    // If this is set to null,
    // then Phan assumes the PHP version which is closest to the minor version
    // of the php executable used to execute phan.
    // TODO: Set this.
    'target_php_version' => null,

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
    "exclude_analysis_directory_list" => [
        'vendor/'
    ],
];
```

`/path/to/phan --init --init-level=3` can alternatively be used to automatically generate .phan/config.php for a project using composer. (--init-level=1 generates a very strict config, --init-level=5 generates a weak config). You may need to manually add some indirect third party dependencies to `directory_list`.

Take a look at [[Incrementally Strengthening Analysis]] for some tips on how to start with a weak analysis and slowly increase the strictness as your code becomes better equipped to be analyzed.

A list of configuration options and their default values can be found at the page [[Phan Config Settings]].

Also, see Phan's own [`.phan/config.php`](https://github.com/phan/phan/blob/master/.phan/config.php) for the (fairly strict) config Phan uses to analyze itself.

# Running Phan in Continuous Integration

This assumes you have set up `.phan/config.php` in the root directory of your git project.

Phan will exit with a non-zero exit code if 1 or more errors are detected (or if Phan failed to run).

Many Continuous integration tools can be used to detect that exit code (And/or parse the generated report file) and cause the build to fail. A list of several examples is below:

- Travis: Example configurations: [for tolerant-php-parser-to-php-ast (simple)](https://github.com/TysonAndre/tolerant-php-parser-to-php-ast/blob/master/.travis.yml), [for phan/phan (Runs self test as part of a shell script)](https://github.com/phan/phan/blob/master/.travis.yml)

- Appveyor (Windows): Example configurations: [Phan's own configuration](https://github.com/phan/phan/blob/1.1.2/.appveyor.yml#L91-L92)

- Jenkins (Enterprise): Similar to other build tools. I've found that the pylint output formatter works well with Jenkins for generating a Violations view (see [issue #184](https://github.com/phan/phan/issues/184)).


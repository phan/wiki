There are several options for getting Phan up and running on your code base.

To get started, choose [a method for getting Phan running on your system](https://github.com/etsy/phan/wiki/Getting-Started#installing-phan) and then [create a config file](https://github.com/etsy/phan/wiki/Getting-Started#creating-a-config-file) so that Phan knows where to look for code and how to analyze it. Take a look at [Installing Phan Dependencies](https://github.com/etsy/phan/wiki/Getting-Started#installing-phan-dependencies) for some help getting Phan's dependencies running.

If you're having trouble getting Phan up and running, get in touch.

[![Gitter](https://badges.gitter.im/etsy/phan.svg)](https://gitter.im/etsy/phan?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

I should be able to respond fairly quickly during US east coast working hours.

# Installing Phan

## Composer

If you're managing dependencies via [Composer](https://getcomposer.org/), you can add Phan to your project by running the following.

If you're running PHP version 7.0.x, run

```sh
composer require --dev "etsy/phan:0.8.x-dev"
```

If you're running PHP version 7.1+, run

```sh
composer require --dev "etsy/phan:dev-master"
```

With the Phan dependency installed, you can do an analysis by running the following (once you create a configuration file).

```sh
./vendor/bin/phan
```

## From Source

Before you begin, you'll want to make sure you have PHP version 7+ with the [php-ast module](https://github.com/nikic/php-ast) installed.

From there, you can clone the Phan source and use composer to install its dependencies.

```sh
git clone https://github.com/etsy/phan.git;
composer install;
```

*NOTE:* If you're running on PHP version 7.0.x and run into problems, you may wish to use the [0.8 branch of Phan](https://github.com/etsy/phan/tree/0.8) (`git checkout 0.8`).

You should now be able to run `./test` to make sure Phan is working correctly, and run `./phan` to run Phan on itself (using its own [`.phan/config.php`](https://github.com/etsy/phan/blob/master/.phan/config.php) configuration).

## From Phan.phar

To run Phan from a Phar package, you can download the Phar and run it.

```sh
curl -L https://github.com/etsy/phan/releases/download/0.8.3/phan.phar -o phan.phar;
```

You'll now be able to run Phan via

```sh
php phan.phar
```

As of this writing, [0.8.3](https://github.com/etsy/phan/releases/tag/0.8.3) is the latest release. You may wish to check [the list of releases](https://github.com/etsy/phan/releases) to see if thats still the latest, as I'll probably forget to update this page with subsequent releases.

## From a Docker Image

Several docker images exist for Phan that include its dependencies (PHP version 7, php-ast). I haven't tried any of them myself, but you could check out these projects.

* https://github.com/cloudflare/docker-phan
* https://github.com/mre/docker-php-phan
* https://github.com/madmuffin1/phan-docker
* [Others that show up now and then](https://www.google.com/webhp#q=phan%20docker)

## From Code Climate

[Code Climate](https://codeclimate.com/) provides a code analysis service that can be configured to run Phan on your code base. You can take a look at [the Code Climate Phan documentation here](https://docs.codeclimate.com/docs/phan).

## From Homebrew

To get Phan running on a Mac with Homebrew, ensure that Homebrew is installed (see [http://brew.sh](http://brew.sh)) and then run the following.

```sh
brew update
brew install php70 php70-ast phan
```

Once that completes successfully, you can check that phan is working correctly by running

```sh
phan -h
```

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

Take a look at [[Incrementally Strengthening Analysis]] for some tips on how to start with a weak analysis and slowly increase the strictness as your code becomes better equipped to be analyzed.


# Installing Phan Dependencies

Phan depends on [PHP](http://php.net/) version 7 or greater and [php-ast](https://github.com/nikic/php-ast) by [Nikita Popov](https://github.com/nikic).

If you don't have a version of PHP 7 installed, you can grab a php7dev Vagrant image or one of the many Docker builds out there.

To compile [php-ast](https://github.com/nikic/php-ast). Something along these lines should do it:

```sh
git clone https://github.com/nikic/php-ast.git
cd php-ast
phpize
./configure
make install
```

And add `extension=ast.so` to your `php.ini` file. Check that it is there with `php -m`.
If it isn't you probably added it to the wrong `php.ini` file. Check `php --ini` to see
where it is looking.

If `phpize` is unavailable on your system, you may need to install the PHP developer
packages which are often available with names such as `php-dev`.

Windows users can grab `ast.dll` directly from [PECL snaps](http://windows.php.net/downloads/pecl/snaps/ast/)

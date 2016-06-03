There are a few ways to get started using Phan.

# Installing Phan

## Composer

If you're managing dependencies via [Composer](https://getcomposer.org/), you can add Phan to your project by running the following.

```sh
composer require --dev etsy/phan;
composer install;
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

You should now be able to run `./test` to make sure Phan is working correctly, and run `./phan` to run Phan on itself (using its own [`.phan/config.php`](https://github.com/etsy/phan/blob/master/.phan/config.php) configuration).

## From Phan.phar

To run Phan from a Phar package, you can download the Phar and run it.

```sh
curl -L https://github.com/etsy/phan/releases/download/0.5/phan.phar -o phan.phar;
```

You'll now be able to run Phan via

```sh
php phan.phar
```

## From a Docker Image

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
    //       should be added to the `directory_list` as
    //       to `excluce_analysis_directory_list`.
    "exclude_analysis_directory_list" => [
        'vendor/'
    ],
];
```

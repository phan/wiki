There are a few ways to get started using Phan.

# Composer

If you're managing dependencies via [Composer](https://getcomposer.org/), you can add Phan to your project by running the following.

```sh
composer require --dev etsy/phan;
composer install;
```

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

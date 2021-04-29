PHP isn’t a language that makes you think of provably correct code. For large mature PHP code bases that have been around since before type-hinting, static analysis needs to be introduced slowly if you want to avoid your team losing their minds.

Phan has a core philosophy of measuring its success by its ability to enable communication between engineers and improve a code base over time rather than by an ability to prove that code is correct. With this comes a product focus for Phan on enabling incremental-strengthening of analysis without forcing users to deal with tens of thousands of issues that you’d have to fix before getting any value out of ongoing static analysis. Phan offers a large range of configuration options for only doing the analysis that’s most important to you.

# Configuration Options

You can configure Phan's analysis via various command-line options and via the [config file](https://github.com/phan/phan/wiki/Phan-Config-Settings) `.phan/config.php` which Phan will look for from the directory in which it's run or in the directory passed in via the `--directory <BASE_DIRECTORY>` CLI option.

Take a look at Phan's own very strict configuration for analyzing itself at [`.phan/config.php`](https://github.com/phan/phan/blob/v4/.phan/config.php) to see how configs work and to see what can be tuned. You can also take a look at [the default configuration](https://github.com/phan/phan/blob/v4/src/Phan/Config.php) to see what an analysis without configuration looks like.

## Relaxed Analysis

When you first begin analyzing your code base, you may want to consider starting with the following configuration (saved to `.phan/config.php`) that only looks at the most critical issues and allows for a reasonable degree of sloppiness.

In a project set up to use the composer autoloader (including for its own subdirectories), this may also be done via `vendor/bin/phan --init --init-level=5`, where 5 is an weak analysis, and 1 would be a very strong analysis. (This probably won't work if autoloading starts at the root of the project instead of a subdirectory.)

```php
<?php
/**
 * This configuration will be read and overlaid on top of the
 * default configuration. Command line arguments will be applied
 * after this file is read.
 *
 * @see src/Phan/Config.php
 * See Config for all configurable options.
 */
return [
    // Supported values: `'5.6'`, `'7.0'`, `'7.1'`, `'7.2'`, `'7.3'`,
    // `'7.4'`, `'8.0'`, `null`.
    // If this is set to `null`,
    // then Phan assumes the PHP version which is closest to the minor version
    // of the php executable used to execute Phan.
    //
    // Note that the **only** effect of choosing `'5.6'` is to infer that functions removed in php 7.0 exist.
    // (See `backward_compatibility_checks` for additional options)
    'target_php_version' => null,

    // Backwards Compatibility Checking. This is slow
    // and expensive, but you should consider running
    // it before upgrading your version of PHP to a
    // new version that has backward compatibility
    // breaks. (Also see target_php_version)
    'backward_compatibility_checks' => false,

    // If true, this run a quick version of checks that takes less
    // time at the cost of not running as thorough
    // an analysis. You should consider setting this
    // to true only when you wish you had more **undiagnosed** issues
    // to fix in your code base.
    'quick_mode' => true,

    // If enabled, check all methods that override a
    // parent method to make sure its signature is
    // compatible with the parent's. This check
    // can add quite a bit of time to the analysis.
    'analyze_signature_compatibility' => false,

    // The minimum severity level to report on. This can be
    // set to Issue::SEVERITY_LOW(0), Issue::SEVERITY_NORMAL(5) or
    // Issue::SEVERITY_CRITICAL(10). Setting it to only
    // critical issues is a good place to start on a big
    // sloppy mature code base.
    'minimum_severity' => 10,

    // If true, missing properties will be created when
    // they are first seen. If false, we'll report an
    // error message if there is an attempt to write
    // to a class property that wasn't explicitly
    // defined.
    'allow_missing_properties' => true,

    // Allow null to be cast as any type and for any
    // type to be cast to null. Setting this to false
    // will cut down on false positives.
    'null_casts_as_any_type' => true,

    // Allow null to be cast as any array-like type.
    // This is an incremental step in migrating away from null_casts_as_any_type.
    // If null_casts_as_any_type is true, this has no effect.
    'null_casts_as_array' => false,

    // Allow any array-like type to be cast to null.
    // This is an incremental step in migrating away from null_casts_as_any_type.
    // If null_casts_as_any_type is true, this has no effect.
    'array_casts_as_null' => false,

    // If enabled, scalars (int, float, bool, true, false, string, null)
    // are treated as if they can cast to each other.
    'scalar_implicit_cast' => true,

    // If this has entries, scalars (int, float, bool, true, false, string, null)
    // are allowed to perform the casts listed.
    // E.g. ['int' => ['float', 'string'], 'float' => ['int'], 'string' => ['int'], 'null' => ['string']]
    // allows casting null to a string, but not vice versa.
    // (subset of scalar_implicit_cast)
    'scalar_implicit_partial' => [],

    // If true, seemingly undeclared variables in the global
    // scope will be ignored. This is useful for projects
    // with complicated cross-file globals that you have no
    // hope of fixing.
    'ignore_undeclared_variables_in_global_scope' => true,

    // Add any issue types (such as 'PhanUndeclaredMethod')
    // to this black-list to inhibit them from being reported.
    'suppress_issue_types' => [
        // 'PhanUndeclaredMethod',
    ],

    // If empty, no filter against issues types will be applied.
    // If this white-list is non-empty, only issues within the list
    // will be emitted by Phan.
    'whitelist_issue_types' => [
        // 'PhanAccessMethodPrivate',
    ],
];
```

With such a configuration, the following very questionable code will pass analysis without any issues.

```php
<?php
error_reporting(E_ERROR);

class B {
    /**
     * @param string $p
     * @return string
     */
    function g($p) {
        if (!$p) {
            return null;
        }
        return $p;
    }
}

class C extends B {
    function f($p) {
        return $this->$p[0];
    }

    /**
     * @param int $p
     * @return int
     */
    function g($p) {
        $this->p = $p;
        $this->property = [42];
        return $this->f($p);
    }

}

print (new C)->g('property' . $undeclared_global) . "\n";
```

[PHP will handle that code without failing](https://3v4l.org/q2WM6), so perhaps it's not the most important thing to focus on initially. Once you've taken care of the most critical issues in your code base, you can start incrementally strengthening Phan by updating the configuration values to get to the point where the above code would cause the following issues to be emitted.

```
passes.php:11 PhanTypeMismatchReturn Returning type null but g() is declared to return string
passes.php:19 PhanCompatiblePHP7 Expression may not be PHP 7 compatible
passes.php:26 PhanSignatureMismatch Declaration of function g(int $p) : int should be compatible with function g(string $p) : string defined in passes.php:9
passes.php:27 PhanUndeclaredProperty Reference to undeclared property p
passes.php:28 PhanUndeclaredProperty Reference to undeclared property property
passes.php:34 PhanTypeMismatchArgument Argument 1 (p) is string but \C::g() takes int defined at passes.php:26
```

## Just Backward Compatibility

If you're in the process of migrating from PHP5 to PHP7, you may wish to only scan for backward compatibility issues that you'll need to fix before the switch.

The following configuration will ignore all issue types but backward compatibility issues.

If you are migrating from PHP 5 to PHP 7,
you should also look into using
[php7cc (no longer maintained)](https://github.com/sstalle/php7cc)
and [php7mar](https://github.com/Alexia/php7mar),
which have different backwards compatibility checks.

If you are still using versions of php older than 5.6,
`PHP53CompatibilityPlugin` may be worth looking into if you are not running
syntax checks for php 5.3 through another method such as
`InvokePHPNativeSyntaxCheckPlugin` or `phan --native-syntax-check php53`
(see .phan/plugins/README.md).

```php
<?php

/**
 * This configuration will be read and overlaid on top of the
 * default configuration. Command line arguments will be applied
 * after this file is read.
 *
 * @see src/Phan/Config.php
 * See Config for all configurable options.
 */
return [
    // Backwards Compatibility Checking. This is slow
    // and expensive, but you should consider running
    // it before upgrading your version of PHP to a
    // new version that has backward compatibility
    // breaks.
    //
    // If you are migrating from PHP 5 to PHP 7,
    // you should also look into using
    // [php7cc (no longer maintained)](https://github.com/sstalle/php7cc)
    // and [php7mar](https://github.com/Alexia/php7mar),
    // which have different backwards compatibility checks.
    //
    // If you are still using versions of php older than 5.6,
    // `PHP53CompatibilityPlugin` may be worth looking into if you are not running
    // syntax checks for php 5.3 through another method such as
    // `InvokePHPNativeSyntaxCheckPlugin` (see .phan/plugins/README.md).
    //
    // You may wish to disable 'redundant_condition_detection'
    // until your project drops php 5 support.
    'backward_compatibility_checks' => true,

    // Set this to false to emit
    // PhanUndeclaredFunction issues for internal functions
    // that Phan has signatures for,
    // but aren't available in the codebase or the
    // internal functions used to run phan
    'ignore_undeclared_functions_with_known_signatures' => false,

    // If empty, no filter against issues types will be applied.
    // If this white-list is non-empty, only issues within the list
    // will be emitted by Phan.
    'whitelist_issue_types' => [
        'PhanCompatiblePHP7',  // This only checks for **syntax** where the parsing may have changed. This check is enabled by `backward_compatibility_checks`
        'PhanDeprecatedFunctionInternal',  // Warns about a few functions deprecated in 7.0 and later.
        'PhanUndeclaredFunction',  // Check for removed functions such as split() that were deprecated in php 5.x and removed in php 7.0.
    ],
    // Check that 'php --syntax-check' passes for the files being analyzed by Phan.
    'plugins' => ['InvokePHPNativeSyntaxCheckPlugin'],
    // You may wish to add paths both to the older php binary and the newer php binary
    // in 'plugin_config' => ['php_native_syntax_check_binaries' => [...]] to avoid syntax errors
    // while the migration is in progress, unless another tool already does that.
];
```

With the above config, the following code will emit the following issues.

```php
<?php
echo $foo->$bar['baz'];
Foo::$bar['baz']();
$foo->$bar['baz']();
strlen($foo->$bar['baz']);
class C {
    public $bb = 2;
}
class T {
    public $b = null;
    function fn($a) {
      $this->b = new C;
      echo $this->b->$a[1];
    }
}
$t = new T;
$t->fn(['aa','bb','cc']);
// tests that should pass without warning below
class Test {
    public static $vals = array('a' => 'A', 'b' => 'B');
    public static function get($letter) {
        return self::$vals[$letter];
    }
	public function fn($letter) {
        return $this->vals[$letter];
    }
}
```

with issues:

```
fails.php:2 PhanCompatiblePHP7 Expression may not be PHP 7 compatible
fails.php:3 PhanCompatiblePHP7 Expression may not be PHP 7 compatible
fails.php:4 PhanCompatiblePHP7 Expression may not be PHP 7 compatible
fails.php:5 PhanCompatiblePHP7 Expression may not be PHP 7 compatible
fails.php:13 PhanCompatiblePHP7 Expression may not be PHP 7 compatible
```

# Suppressing Issues

As you ramp-up your analysis, you'll often be confronted with an overwhelming number of possibly legit issues that by some miracle aren't destroying your product that you just don't have time to fix individually.

This is going to feel gross, but if you want to get to a place where you can prevent new issues from being introduced in new code, you're going to want to add `@suppress` annotations to existing issues. The world isn't fair, nothing is perfect and you have much more important things to work on.

Take a look at [Annotating Your Source Code](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#suppress) for details on how to suppress issues.

Once you get a clean run for a new issue type and can enable the analysis, you'll want to let folks know that adding new `@suppress` annotations is uncool, and that true heroes remove existing `@suppress` annotations.

# Speeding up Analysis

As you start playing around with figuring out the right strength for Phan, you'll end up running Phan over and over. A speedy analysis will help you to prevent losing your mind.

The easiest way to speed up Phan is to use the `--processes <number>` command-line option to choose how many CPUs the analysis phase runs on. A good number of processes to use is one or two less than the available number of cores on your machine, so long as you have a good amount of available memory.

See [[Speeding Up Phan Analysis]] for additional ways to speed up Phan analysis.

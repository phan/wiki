PHP isn’t a language that makes you think of provably correct code. For large mature PHP code bases that have been around since before type-hinting, introducing static analysis comes with some fun constraints.

Phan has a core philosophy of measuring its success by its ability to enable communication between engineers and improve a code base over time rather than by an ability to prove that code is correct. With this comes a product focus for Phan on enabling incremental-strengthening of analysis without forcing users to deal with tens of thousands of issues that you’d have to fix before getting any value out of ongoing static analysis. Phan offers a large range of configuration options for only doing the analysis that’s most important to you.

# Configuration Options

You can configure Phan's analysis via a small number of command-line options and via the config file `.phan/config.php` which Phan will look for from the directory in which it's run or in the directory passed in via the `--directory <BASE_DIRECTORY>` CLI option.

When you first begin analyzing your code base, you may want to consider starting with the following configuration (saved to `.phan/config.php`) that only looks at the most critical issues and allows for a reasonable degree of sloppiness.

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
    'backward_compatibility_checks' => false,

    // Run a quick version of checks that takes less
    // time at the cost of not running as thorough
    // an analysis. You should consider setting this
    // to true only when you wish you had more issues
    // to fix in your code base.
    'quick_mode' => true,

    // If enabled, check all methods that override a
    // parent method to make sure its signature is
    // compatible with the parent's. This check
    // can add quite a bit of time to the analysis.
    'analyze_signature_compatibility' => false,

    // The minimum severity level to report on. This can be
    // set to Issue::SEVERITY_LOW, Issue::SEVERITY_NORMAL or
    // Issue::SEVERITY_CRITICAL. Setting it to only
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

    // If enabled, scalars (int, float, bool, string, null)
    // are treated as if they can cast to each other.
    'scalar_implicit_cast' => true,

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

class C {
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

[PHP will handle that code without failing](https://3v4l.org/3GvkL), so perhaps its not the most important thing to focus on initially. Once you've taken care of the most critical issues in your code base, you can start incrementally strengthening Phan by updating the configuration values to get to the point where the above code would cause the following issues to be emitted.

```
./passes.php:6 PhanCompatiblePHP7 Expression may not be PHP 7 compatible
./passes.php:14 PhanUndeclaredProperty Reference to undeclared property p
./passes.php:15 PhanUndeclaredProperty Reference to undeclared property property
./passes.php:21 PhanTypeMismatchArgument Argument 1 (p) is string but \C::g() takes int defined at ./passes.php:13
```
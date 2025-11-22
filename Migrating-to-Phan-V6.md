# Migrating to Phan v6

Phan v6 is a major release with significant improvements to type analysis, support for the latest PHP language features, and performance optimizations. However, it includes several breaking changes that you should be aware of when upgrading.

## Quick Summary of Breaking Changes

- **PHP 8.1+ required** - Minimum version to run Phan (not necessarily your target version)
- **AST 1.1.3+ required** for PHP 8.4+ analysis support
- **`-i` CLI flag** changed from `--ignore-undeclared` to `--incremental`
- **Issue type renamed**: `PhanPluginCanUsePHP71Void` → `PhanPluginCanUseVoidReturnType`
- **Config options removed**:
  - `allow_method_param_type_widening`
  - `backward_compatibility_checks` (and related CLI flags)
- **Contravariance always enabled** - Parameter type widening is now always allowed

## Detailed Migration Guide

### 1. PHP Version Requirements

#### Phan Runtime Requirement: PHP 8.1+

Phan v6 **requires PHP 8.1 or higher** to run the analyzer itself. This is independent of the PHP version your code targets.

```bash
# Check your PHP version
php --version

# If you need to use PHP 8.1+, you can still analyze code for PHP 7.x or lower
# with the appropriate configuration
```

To analyze code for different PHP versions, use the `target_php_version` config option:

```php
// .phan/config.php
return [
    'target_php_version' => '7.4',  // Analyze for PHP 7.4
    // ... rest of config
];
```

#### AST Version Requirement: 1.1.3+ for PHP 8.4+

If you're analyzing PHP 8.4 or 8.5 code, you must have the `php-ast` extension version 1.1.3 or higher installed.

```bash
# Check your AST version
php -r "echo phpversion('ast');"

# Update the extension if needed
pecl install --upgrade ast
```

### 2. CLI Flag Changes

#### The `-i` Flag

In v5, `-i` was an alias for `--ignore-undeclared`. In v6, it's an alias for the new `--incremental` flag.

**Before (v5):**
```bash
phan -i  # Would ignore undeclared definitions
```

**After (v6):**
```bash
phan -i  # Now enables incremental analysis (much faster on re-runs)
```

**If you need to ignore undeclared definitions**, use the config option instead:

```php
// .phan/config.php
return [
    'ignore_undeclared_variables_in_global_scope' => true,
    // ... or use per-line suppression:
    // @phan-suppress PhanUndeclaredVariable
];
```

#### New Incremental Analysis Flags

Phan v6 introduces **incremental analysis** - only changed files and dependents are re-analyzed on subsequent runs.

```bash
# Enable incremental analysis
phan --incremental
# or
phan -i

# Force a full re-analysis, ignoring the incremental manifest
phan --force-full-analysis

# Disable incremental analysis (useful if enabled in config.php)
phan --no-incremental
# or
phan -N
```

For detailed information, see [[Incremental-Analysis]].

#### Other New CLI Flags

```bash
# Analyze only files provided on the command line
phan -n file1.php file2.php
# This now limits analysis to just these files, not the entire project

# Analyze a subdirectory/module with better performance
phan --subdirectory-only /path/to/module

# Tune memory/performance thresholds
phan --ast-trim-max-elements-per-level 256
phan --ast-trim-max-total-elements 512
phan --max-union-type-set-size 1024
```

### 3. Issue Type Renamings

#### `PhanPluginCanUsePHP71Void` → `PhanPluginCanUseVoidReturnType`

This issue type was renamed to be more accurate and version-agnostic. Since PHP 7.1, methods can declare `void` return type.

If you have suppressions for the old name:

```php
// OLD (v5) - will no longer work
/** @phan-suppress PhanPluginCanUsePHP71Void */
public function doSomething() {}

// NEW (v6) - use the new name
/** @phan-suppress PhanPluginCanUseVoidReturnType */
public function doSomething(): void {}
```

Update your baseline file if you're using one:

```bash
phan --save-baseline .phan/.baseline.php
```

### 4. Config Option Removals

#### `allow_method_param_type_widening` - Removed

In v5, you could disable parameter type widening (contravariance). This is now always enabled, as it's essential for proper type safety.

**Before (v5):**
```php
// .phan/config.php
return [
    'allow_method_param_type_widening' => false,  // NO LONGER SUPPORTED
];
```

**After (v6):**
```php
// Contravariance is always enabled, no config needed
// This is correct PHP type system behavior:
interface Animal {}
interface Dog extends Animal {}

// This is now always allowed (correctly):
function acceptDogs(Dog $dog) {}
function acceptAnimals(Animal $animal) {} // Wider parameter type is allowed
```

If you were relying on this to suppress warnings, you now need to fix the actual type signatures.

#### `backward_compatibility_checks` - Removed

This option and its related CLI flags (`--backward-compatibility-checks`, `-b`) have been removed. Backward compatibility checking is no longer provided as a separate mode.

**Before (v5):**
```bash
phan --backward-compatibility-checks
phan -b
```

**After (v6):**
These flags no longer exist. Use standard Phan analysis which includes all necessary checks.

If you were using this for specific checks, consider:
1. Using Phan's standard analysis mode (which is stricter)
2. Using specific plugins if available
3. Suppressing false positives with `@phan-suppress` as needed

### 5. Stricter Contravariance Handling

Related to removing `allow_method_param_type_widening`, v6 more strictly enforces proper contravariance in method overrides:

```php
class Animal {}
class Dog extends Animal {}

interface AnimalProcessor {
    public function process(Animal $animal): void;
}

// CORRECT in v6: accepts wider type (Animal) instead of narrower (Dog)
class DogProcessor implements AnimalProcessor {
    public function process(Animal $animal): void {}
}

// ERROR in v6: tries to accept narrower type
class AnimalOnlyProcessor implements AnimalProcessor {
    public function process(Dog $dog): void {}  // ERROR: Parameter type narrowing violates Liskov substitution
}
```

### 6. New Features Available in V6

While migrating, take advantage of these new features:

- **Incremental Analysis**: Significantly speeds up re-runs. See [[Incremental-Analysis]].
- **Enhanced Generics**: Better support for `@template`, constraints, and variance. See [[Generic-Types-V6]].
- **PHP 8.4/8.5 Support**: Property hooks, `#[Deprecated]`, pipe operator, and more. See [[PHP-8.4-8.5-Support]].
- **Multiline Doc Comments**: Better formatting of complex type annotations.
- **Performance**: 5-15% faster analysis with optional C extension and other optimizations.

### 7. Upgrading Process

1. **Update Phan**:
   ```bash
   composer require --dev phan/phan:^6.0
   ```

2. **Update php-ast if needed**:
   ```bash
   pecl install --upgrade ast
   ```

3. **Test your analysis**:
   ```bash
   phan
   ```

4. **Review new issues**: You may see new issue types that were previously not detected.

5. **Update config**:
   - Remove deprecated options
   - Consider enabling incremental analysis with `--incremental`

6. **Update suppressions**:
   - Review any suppressions with renamed issue types
   - Regenerate baseline if using one

### 8. Common Migration Issues

#### Issue: "The `-i` flag is using incremental analysis instead of ignoring undeclared"

**Solution**: Use `--no-config-file` or update your scripts. See [[Using-Phan-From-Command-Line]] for details.

#### Issue: "Parameter type widening errors in my existing code"

**Solution**: This is now correctly enforced. Update your method signatures to match the interface/parent class contracts. Use contravariant (wider) types in implementations.

#### Issue: "Unknown config option `allow_method_param_type_widening`"

**Solution**: Remove this option from your config. Contravariance is always enabled now.

#### Issue: "Custom baseline file is no longer compatible"

**Solution**: Regenerate your baseline:
```bash
phan --save-baseline .phan/.baseline.php
```

v6 uses symbol-based baselines which are more stable across code changes.

### 9. Support and Questions

For more information:
- See the [[Home]] page for all documentation
- Check [[Frequently-Asked-Questions]] for common issues
- File issues on [GitHub](https://github.com/phan/phan/issues)
- See [[Incremental-Analysis]], [[Generic-Types-V6]], and [[PHP-8.4-8.5-Support]] for specific feature details

### Changelog

For a complete list of changes in Phan v6, see the [NEWS.md](https://github.com/phan/phan/blob/master/NEWS.md) file in the repository.

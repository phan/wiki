# Tooling and Suppression Baselines in Phan V6

Phan v6 introduces powerful tools for managing suppressions and baselines, making it easier to handle legacy code and track analysis progress over time.

## Table of Contents

1. [Suppression Baselines](#suppression-baselines)
2. [add_suppressions.php Tool](#add_suppressionphp-tool)
3. [Baseline Generation](#baseline-generation)
4. [Managing Issues](#managing-issues)

---

## Suppression Baselines

### Overview

A **baseline** is a stored record of all known issues in your codebase. Instead of fixing every issue, you can save the current state and focus on preventing new issues.

### Key Benefits of Baselines in V6

1. **Symbol-based tracking** - Issues are tied to symbols (class/method/function) not line numbers
2. **Stable across changes** - Code movement doesn't invalidate suppressions
3. **Better debugging** - Normalized names for closures and anonymous classes
4. **Incremental improvement** - Track progress over time

### Generating a Baseline

```bash
# Create a baseline from current analysis
phan --save-baseline .phan/.baseline.php

# Analyze subsequent runs against the baseline
phan --use-baseline .phan/.baseline.php
```

### Baseline File Structure

```php
// .phan/.baseline.php
return [
    'version' => '6.0.0',
    // Issues grouped by symbol
    'Group\\ClassA::methodA' => [
        'PhanUnreferencedPublicMethod' => 2,  // Count of suppressions
    ],
    'Group\\ClassB' => [
        'PhanUnreferencedPrivateProperty' => 1,
    ],
    // Global issues
    'global' => [
        'PhanUndeclaredVariable' => 3,
    ],
];
```

### Tracking Symbol-based Issues

**V6 Feature**: Issues are now stored by symbol instead of line number.

This means:
- Moving a method doesn't invalidate its baseline entry
- Renaming a variable in a method doesn't affect the baseline
- Refactoring code structure preserves baseline accuracy

```php
class User {
    /**
     * @suppress PhanUnreferencedPublicMethod
     */
    public function oldMethod() {
        // Suppression stays valid even if this moves
    }
}
```

### Normalized Closure Names

Closures and anonymous classes are normalized for stability:

```php
// Before: closures had hash-based names
// After: normalized to position-independent names

// Anonymous class
$helper = new class {
    public function help() {}  // Baseline: AnonymousClass::help
};

// Closure
$fn = function() {};  // Baseline: closure at file:line
```

---

## add_suppressions.php Tool

### Overview

The `add_suppressions.php` tool helps **batch-suppress** specific issue types across your codebase without manually editing each file.

### Installation

```bash
# The tool is included with Phan
php vendor/phan/phan/tool/add_suppressions.php --help
```

### Basic Usage

```bash
# Suppress specific issue type in all files
php tool/add_suppressions.php \
    --suppress-issue-type PhanUnreferencedPublicMethod

# Suppress multiple issue types
php tool/add_suppressions.php \
    --suppress-issue-type PhanUnreferencedPublicMethod \
    --suppress-issue-type PhanUnreferencedPrivateProperty
```

### Configuration

Create a config file for the tool:

```php
// .phan/suppressions_config.php
return [
    'suppress_issue_types' => [
        'PhanUnreferencedPublicMethod',
        'PhanUnreferencedPrivateProperty',
    ],
    'directories' => [
        'src/',
        'lib/',
    ],
    'exclude_patterns' => [
        '/tests/',
        '/vendor/',
    ],
];
```

Then run:

```bash
php tool/add_suppressions.php --config .phan/suppressions_config.php
```

### Dry Run

Test suppressions before applying:

```bash
php tool/add_suppressions.php \
    --suppress-issue-type PhanUnreferencedPublicMethod \
    --dry-run
```

### Output Example

The tool modifies files by adding `@suppress` comments:

```php
// Before
public function legacyMethod() {
    // Not used anywhere
}

// After
/**
 * @suppress PhanUnreferencedPublicMethod
 */
public function legacyMethod() {
    // Not used anywhere
}
```

### Use Cases

#### Case 1: Legacy Code Integration

When integrating old code:

```bash
# Suppress all issues in legacy directory
php tool/add_suppressions.php \
    --directories legacy/ \
    --suppress-issue-type PhanUnreferencedPublicMethod \
    --suppress-issue-type PhanTypeMismatchArgument \
    --suppress-issue-type PhanUndeclaredVariable
```

#### Case 2: Gradual Type Safety

Suppress deprecated patterns while adding new ones:

```bash
# Suppress usage of deprecated functions
php tool/add_suppressions.php \
    --suppress-issue-type PhanDeprecatedFunction
```

#### Case 3: Third-party Integration

Suppress issues from dynamically loaded code:

```bash
# Suppress undeclared in plugin directory
php tool/add_suppressions.php \
    --directories plugins/ \
    --suppress-issue-type PhanUndeclaredClass \
    --suppress-issue-type PhanUndeclaredFunction
```

---

## Baseline Generation

### Workflow: From Issues to Baseline

```bash
# Step 1: Run Phan and get issue count
phan 2>&1 | tail -5
# Output: 247 issues found

# Step 2: Save the current state as baseline
phan --save-baseline .phan/.baseline.php

# Step 3: Verify baseline was saved
ls -la .phan/.baseline.php

# Step 4: Update analysis config
# .phan/config.php
return [
    'use_baseline' => true,
    'baseline' => '.phan/.baseline.php',
];
```

### Interpreting Baseline Output

```bash
# Run against baseline
phan --use-baseline .phan/.baseline.php

# Output
# 0 issues found

# This means: no NEW issues beyond the baseline
```

### Updating Baseline

As you fix issues, update the baseline:

```bash
# Fix some issues in your code
# Then update the baseline
phan --save-baseline .phan/.baseline.php

# Verify fewer issues are in baseline
# The counts should decrease
```

### Baseline Gradual Improvements

Track improvements over time:

```bash
# Initial baseline
phan --save-baseline .phan/.baseline.php
# 247 issues

# After fixes
phan --save-baseline .phan/.baseline.php
# 198 issues (improved by 49)

# Continue improving...
phan --save-baseline .phan/.baseline.php
# 125 issues (improved by 73)
```

---

## Managing Issues

### Issue Suppression Strategies

#### Strategy 1: Per-Line Suppression

Suppress specific issues at their location:

```php
/** @suppress PhanTypeMismatchArgument */
processValue($wrongType);
```

**Best for**: Isolated issues, special cases

#### Strategy 2: Per-Symbol Suppression

Suppress all issues in a function/method:

```php
/**
 * @suppress PhanUnreferencedPublicMethod, PhanTypeMismatchReturn
 */
public function legacyMethod() {
    // Multiple suppressions
}
```

**Best for**: Entire functions needing suppression

#### Strategy 3: Per-Class Suppression

Suppress all issues in a class:

```php
/**
 * @suppress PhanUnreferencedPublicMethod
 * @suppress PhanTypeMismatchPropertyAssignment
 */
class LegacyClass {
    // All methods and properties have these suppressions
}
```

**Best for**: Legacy classes being phased out

#### Strategy 4: Per-File Suppression

Suppress all issues in a file:

```php
<?php
/**
 * @phan-file-suppress PhanUnreferencedPublicMethod
 * @phan-file-suppress PhanTypeMismatchArgument
 */

class FirstClass {
    public function method() {}
}
```

**Best for**: Generated code, special cases

#### Strategy 5: Baseline Suppression

Track all issues without suppressing in code:

```bash
# Create baseline
phan --save-baseline .phan/.baseline.php

# No suppressions in code - baseline contains all issues
```

**Best for**: Tracking and gradually fixing issues

### Choosing the Right Strategy

| Strategy | Use Case | Maintenance |
|----------|----------|-------------|
| Per-line | Specific exceptions | High (per-line) |
| Per-symbol | Function-level | Medium |
| Per-class | Legacy classes | Low |
| Per-file | Generated files | Very Low |
| Baseline | Progressive fixes | Continuous |

---

## Working with Baselines in CI/CD

### GitHub Actions

```yaml
name: Static Analysis

on: [push, pull_request]

jobs:
  phan:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: ast

      - name: Install dependencies
        run: composer install

      - name: Generate baseline
        run: phan --save-baseline .phan/.baseline.php

      - name: Run Phan
        run: phan --use-baseline .phan/.baseline.php

      - name: Store baseline
        uses: actions/upload-artifact@v3
        if: always()
        with:
          name: baseline
          path: .phan/.baseline.php
```

### GitLab CI

```yaml
static_analysis:
  stage: test
  image: php:8.2
  before_script:
    - apt-get update && apt-get install -y autoconf gcc make
    - pecl install ast
    - docker-php-ext-enable ast
    - composer install
  script:
    - phan --save-baseline .phan/.baseline.php
    - phan --use-baseline .phan/.baseline.php
  artifacts:
    paths:
      - .phan/.baseline.php
    expire_in: 1 month
```

### Jenkins

```groovy
pipeline {
    agent any

    stages {
        stage('Install') {
            steps {
                sh 'composer install'
            }
        }
        stage('Baseline') {
            steps {
                sh 'vendor/bin/phan --save-baseline .phan/.baseline.php'
            }
        }
        stage('Analysis') {
            steps {
                sh 'vendor/bin/phan --use-baseline .phan/.baseline.php'
            }
        }
    }

    post {
        always {
            archiveArtifacts artifacts: '.phan/.baseline.php'
        }
    }
}
```

---

## Best Practices

### Do's ✓

✓ Use baselines for tracking issues over time
✓ Update baselines after fixing issues
✓ Store baselines in version control
✓ Use per-line suppressions for exceptions
✓ Document why suppressions exist
✓ Review suppressions regularly

### Don'ts ✗

✗ Don't suppress all issues initially
✗ Don't leave stale suppressions
✗ Don't suppress without understanding the issue
✗ Don't regenerate baselines frequently in CI
✗ Don't commit baseline files without review
✗ Don't suppress legitimate type errors

### Suppression Documentation

Always document why you're suppressing:

```php
/**
 * @suppress PhanTypeMismatchArgument
 * The API returns a different type than documented.
 * See: https://github.com/api/issue/1234
 */
$value = getWeirdType();
processNormalType($value);
```

### Regular Baseline Review

```bash
#!/bin/bash
# Review baseline changes
git diff .phan/.baseline.php

# Count issues by type
php -r "
\$b = include '.phan/.baseline.php';
\$counts = array_combine(
    array_keys(\$b),
    array_map(fn(\$v) => array_sum((array)\$v), \$b)
);
arsort(\$counts);
foreach (\$counts as \$k => \$v) {
    printf(\"%s: %d\n\", \$k, \$v);
}
"
```

---

## Further Reading

- [[Migrating-to-Phan-V6]] - Migration guide
- [[Using-Phan-From-Command-Line]] - CLI reference
- `tool/SuppressionsToolImplementation.md` - Technical details
- [[Issue-Types-Caught-by-Phan]] - All issue types

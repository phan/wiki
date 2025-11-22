# Incremental Analysis

**New in Phan v6:** Incremental analysis significantly speeds up re-runs of Phan by only analyzing files that have changed and their dependents, rather than re-analyzing the entire codebase.

## ⚠️ Important: Experimental Feature

**Incremental analysis is experimental and may miss issues.**

When using incremental analysis, be aware of the following limitations:

- **Only class/interface/trait dependencies are tracked.** Function calls and other types of dependencies are not tracked, which means changes to functions may not trigger re-analysis of files that call those functions.
- **Non-class dependencies are missed.** If you modify a function or global constant that other files use, those files won't be automatically re-analyzed.
- **Potential for false negatives.** Issues that would appear in a full analysis might not appear after making certain types of changes.

### Recommended Usage:

- Use incremental analysis for **faster feedback during development** on class-based changes
- Run **full analysis regularly** (e.g., before commits) with `phan --force-full-analysis`
- Use in **CI/CD pipelines cautiously** - ensure you run full analysis checks
- Consider incremental analysis primarily for **IDE integration and quick feedback loops**

For guaranteed accuracy, always run periodic full analyses:

```bash
# Run a full re-analysis before committing code
phan --force-full-analysis

# Or if incremental is enabled in config, use:
phan -N  # Disable incremental for this run
```

## Quick Start

Enable incremental analysis with:

```bash
# Using the flag
phan --incremental

# Or the short form
phan -i

# On subsequent runs, only changed files will be re-analyzed
phan -i
```

Incremental analysis is configured and managed automatically. On the first run, Phan creates a manifest file (`.phan/.phan-incremental.php` by default) that tracks which files have been analyzed and their dependencies.

## How It Works

When you run Phan with `--incremental`:

1. **First Run**: Phan analyzes all files and creates a manifest storing:
   - File modification times
   - File hashes
   - Dependencies between files
   - Analysis results

2. **Subsequent Runs**: Phan:
   - Checks which files have changed (by modification time and hash)
   - Detects files that depend on the changed files
   - Re-analyzes only the changed files and their dependents
   - Updates the manifest with new results

3. **Impact**: For large projects, this can be **10-50x faster** than full analysis on re-runs.

### Example Performance Improvement

```
Full analysis (first run):        45 seconds
Full analysis (no changes):       45 seconds (v5)
Incremental analysis (no changes): 2 seconds (v6 with -i)

Change one file:
Full analysis:                    45 seconds
Incremental analysis:              5 seconds (much faster!)
```

## Configuration

### Enable/Disable Incremental Analysis

In `config.php`:

```php
return [
    // Enable incremental analysis by default
    'use_incremental_analysis' => true,

    // Specify where the manifest file is stored
    // (default: '.phan/.phan-incremental.php' in project root)
    'incremental_analysis_file' => '.phan/.phan-incremental.php',
];
```

### CLI Flags

```bash
# Enable incremental analysis
phan --incremental
phan -i

# Disable incremental analysis (useful if enabled in config)
phan --no-incremental
phan -N

# Force a full re-analysis, ignoring the manifest
phan --force-full-analysis

# Analyze only specific files with manifest management
phan --incremental file1.php file2.php
```

## When to Rebuild the Manifest

The incremental analysis manifest should be **invalidated and rebuilt** in these situations:

### 1. Phan or PHP Version Changes

```bash
# After upgrading Phan or PHP
phan --force-full-analysis

# Or delete the manifest to force a rebuild
rm .phan/.phan-incremental.php
phan
```

### 2. Config File Changes

Configuration changes (like adding/removing file directories) should trigger a rebuild:

```bash
# If you modified .phan/config.php
phan --force-full-analysis
```

### 3. External Dependencies Change

If third-party code in `vendor/` changes:

```bash
# Rebuild to account for vendor changes
phan --force-full-analysis
```

### 4. Git Workflow Issues

If you've switched branches or reset files:

```bash
# The safest approach after major VCS changes
phan --force-full-analysis
```

## Git Integration

Phan v6 includes **git-style config discovery**. Phan will search parent directories for `.phan/config.php`:

```bash
# Running from a subdirectory still finds the root config
cd /path/to/project/src
phan  # Finds /path/to/project/.phan/config.php

# Or use --subdirectory-only to analyze just that module
phan --subdirectory-only
```

## Advanced Usage

### Analyzing Specific Modules

Use `--subdirectory-only` for faster focused analysis of a specific module:

```bash
# Analyze only src/Module/A (faster than full project analysis)
cd src/Module/A
phan --subdirectory-only
```

This mode:
- Still loads dependency information from the full project
- Only analyzes files in the current subdirectory
- Results in faster turnaround for focused work

### Daemon Mode Integration

Incremental analysis works with daemon mode for real-time analysis:

```bash
# Start daemon with incremental analysis
phan --daemon --incremental

# Request analysis in your editor
# Only changed files will be re-analyzed
```

## Troubleshooting

### Issue: "Analysis results differ with --force-full-analysis"

**Cause**: The manifest may have become corrupted or inconsistent.

**Solution**: Rebuild the manifest:
```bash
phan --force-full-analysis
```

### Issue: "Incremental analysis seems slower"

**Cause**: Overhead of manifest management may outweigh benefits on small projects.

**Solution**: Disable incremental analysis for projects < 5MB of PHP code:
```bash
phan  # No -i flag for small projects
```

### Issue: "Changes to external dependencies not detected"

**Cause**: Incremental analysis doesn't automatically detect `vendor/` changes.

**Solution**: Rebuild the manifest after composer operations:
```bash
composer update
phan --force-full-analysis
```

### Issue: "False positives after git operations"

**Cause**: File modification times may be inconsistent after branch switches.

**Solution**: Rebuild the manifest:
```bash
git checkout develop
phan --force-full-analysis
```

## CI/CD Integration

### GitHub Actions Example

```yaml
name: Static Analysis

on: [push, pull_request]

jobs:
  phan:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
        with:
          fetch-depth: 0  # Full history for better incremental analysis

      - name: Install PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: ast

      - name: Install dependencies
        run: composer install

      # First run: full analysis
      - name: Run Phan (full)
        if: github.event_name == 'push' && github.ref == 'refs/heads/main'
        run: phan --force-full-analysis

      # PR runs: incremental analysis
      - name: Run Phan (incremental)
        if: github.event_name == 'pull_request'
        run: phan --incremental
```

### GitLab CI Example

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
    # Cache the manifest between runs
    - phan --incremental
  cache:
    paths:
      - .phan/.phan-incremental.php
    key: phan-manifest
```

## Performance Tuning

### Memory Optimization

Phan v6 includes automatic memory optimization for large codebases:

```php
// .phan/config.php
return [
    'use_incremental_analysis' => true,

    // Summarize large literal arrays to reduce memory (default: 256)
    'ast_trim_max_elements_per_level' => 256,

    // Maximum total literal array elements (default: 512)
    'ast_trim_max_total_elements' => 512,

    // Clamp union types when they get too large (default: 1024)
    'max_union_type_set_size' => 1024,
];
```

These settings work with incremental analysis to optimize large projects.

## Known Limitations

1. **Daemon mode** requires clearing the manifest periodically for accuracy
2. **Baseline files** generated with incremental analysis are less stable than full-analysis baselines
3. **Plugin analysis** may not fully account for cross-file dependencies
4. **Stub files** in `.phan/stubs/` should trigger rebuild if modified

## Tips and Best Practices

### Do

✓ Enable incremental analysis for projects > 5MB of code
✓ Use `--force-full-analysis` before CI/CD runs
✓ Cache the manifest file in CI environments
✓ Rebuild after major dependency updates
✓ Use `--subdirectory-only` for focused development

### Don't

✗ Don't share the manifest file between different projects
✗ Don't rely on incremental analysis after git history rewrites
✗ Don't ignore rebuild suggestions in error messages
✗ Don't use incremental analysis with incompatible older PHP versions

## Further Reading

- [[Migrating-to-Phan-V6]] - Breaking changes and upgrade guide
- [[Memory-and-Performance-Optimizations]] - Performance tuning details
- [[Using-Phan-From-Command-Line]] - CLI flag reference
- [NEWS.md](https://github.com/phan/phan/blob/master/NEWS.md) - Full changelog

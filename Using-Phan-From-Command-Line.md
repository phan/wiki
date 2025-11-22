# Using Phan From the Command Line (Phan V6)

Complete reference for Phan v6 command-line flags, options, and usage patterns.

## Quick Reference

```bash
# Basic analysis
phan

# Analysis with config file
phan --config-file .phan/config.php

# No config file (analyze provided files only)
phan -n file1.php file2.php

# Incremental analysis (faster re-runs)
phan --incremental

# Force full re-analysis
phan --force-full-analysis

# Save baseline for issue tracking
phan --save-baseline .phan/.baseline.php

# Analyze against baseline
phan --load-baseline .phan/.baseline.php
```

## Core Flags

### Configuration

#### `--config-file <FILE>` / `-c`
Specify the configuration file to use.

```bash
phan --config-file .phan/config.php
phan -c custom-config.php
```

#### `-n` / `--no-config-file`
**New in V6**: When used with file arguments, limits analysis to just the provided files (ignores the rest of the project).

```bash
# Quick analysis of specific files
phan -n src/User.php src/Product.php

# Without -n, would analyze entire configured project
phan src/User.php  # Analyzes entire project
```

This is useful for:
- Quick checks during development
- IDE integration
- Focused analysis of specific modules

#### `--project-root-directory <DIR>`
Override the project root directory.

```bash
phan --project-root-directory /path/to/project
```

---

## Incremental Analysis (V6)

Dramatically speeds up re-runs by only analyzing changed files.

### `--incremental` / `-i`
Enable incremental analysis mode.

```bash
# First run - full analysis
phan --incremental
# Creates .phan/.phan-incremental.php manifest

# Subsequent runs - only changed files
phan --incremental
# Much faster!
```

**New in V6**: Previously `-i` was an alias for `--ignore-undeclared`. Now it's for `--incremental`.

### `--no-incremental` / `-N`
Disable incremental analysis (useful if enabled in config).

```bash
phan --no-incremental
```

### `--force-full-analysis`
Force a complete re-analysis, ignoring the incremental manifest.

```bash
# After major changes or dependency updates
phan --force-full-analysis
```

### `--subdirectory-only`
**New in V6**: Analyze only the current subdirectory with better performance.

```bash
cd src/Module/A
phan --subdirectory-only
```

Benefits:
- Faster focused analysis
- Still loads dependency information from full project
- Useful for large monorepos

---

## Baseline Management (V6)

Track issues over time without suppressing them in code.

### `--save-baseline <FILE>`
Generate or update a baseline file.

```bash
# Create baseline
phan --save-baseline .phan/.baseline.php

# Update baseline after fixes
phan --save-baseline .phan/.baseline.php
```

The baseline records all current issues. On subsequent runs:
- Only NEW issues are reported
- Known issues are suppressed automatically
- Allows gradual improvement over time

### `--load-baseline <FILE>`
Use a previously saved baseline.

```bash
phan --load-baseline .phan/.baseline.php
```

Typically configured in `config.php`:

```php
return [
    'load_baseline' => '.phan/.baseline.php',
];
```

### `--save-baseline-in-missing-files-only`
Only save suppressions for files not yet analyzed.

```bash
phan --save-baseline-in-missing-files-only .phan/.baseline.php
```

---

## Performance Tuning (V6)

### `--ast-trim-max-elements-per-level <N>`
Limit array elements per level before summarization.

```bash
# Default: 256
phan --ast-trim-max-elements-per-level 512
```

Lower values = less memory, fewer accurate types
Higher values = more memory, better accuracy

### `--ast-trim-max-total-elements <N>`
Limit total elements in nested arrays.

```bash
# Default: 512
phan --ast-trim-max-total-elements 1024
```

### `--max-union-type-set-size <N>`
Limit union type combinations.

```bash
# Default: 1024
phan --max-union-type-set-size 2048
```

When exceeded, types are intelligently merged to prevent runaway growth.

---

## Output and Reporting

### `-j` / `--processes <NUM>`
Number of parallel analysis processes.

```bash
# Use 4 processes
phan -j 4

# Use optimal for your CPU
phan -j $(nproc)
```

Default: number of CPU cores

### `-o` / `--output-mode <MODE>`
Specify output format.

```bash
phan -o text           # Human-readable (default)
phan -o json           # JSON output
phan -o csv            # CSV format
phan -o checkstyle     # Checkstyle format (for CI)
```

### `-f` / `--output-file <FILE>`
Write output to file instead of stdout.

```bash
phan -f analysis-results.txt
phan -o json -f results.json
```

### `--color`
Enable colored output.

```bash
phan --color
```

### `--no-color`
Disable colored output.

```bash
phan --no-color
```

### `-q` / `--quiet`
Only show errors, no summary.

```bash
phan -q
```

---

## Issue Control

### `--issue-handlers <HANDLERS>`
Specify how to handle specific issue types.

```bash
# Suppress specific issues
phan --issue-handlers PhanUnreferencedPublicMethod:suppress

# Warn on others
phan --issue-handlers PhanDeprecatedFunction:warn
```

### `--suppress-issue-type <TYPE>`
Suppress a specific issue type globally.

```bash
phan --suppress-issue-type PhanUnreferencedPublicMethod
```

### `--show-suggestions`
Show suggested fixes (if available).

```bash
phan --show-suggestions
```

---

## Analysis Scope

### `--analyze-twice`
Run analysis twice to catch more issues.

```bash
phan --analyze-twice
```

Slower but more thorough. Useful for complex codebases.

### `--skip-phpstan-baseline`
Don't use phpstan baseline (if configured).

```bash
phan --skip-phpstan-baseline
```

---

## PHP Version and Compatibility

### `--target-php-version <VERSION>`
Set the PHP version to analyze for.

```bash
phan --target-php-version 8.2
phan --target-php-version 7.4
```

Default: version from config or current PHP version

### `--minimum-target-php-version <VERSION>`
**Phan v6**: Specify minimum target version.

```bash
phan --minimum-target-php-version 8.0
```

---

## Plugin Management

### `--load-plugins <PLUGINS>`
Load specific plugins.

```bash
phan --load-plugins PregRegexPlugin,PHPDocPlugin
```

Multiple plugins separated by comma.

### `-i` changed in V6
**Breaking Change**: The `-i` flag is now `--incremental` (not `--ignore-undeclared`).

```bash
# Old (v5)
phan -i  # Ignored undeclared variables

# New (v6)
phan -i  # Enables incremental analysis
```

---

## Daemon Mode

### `--daemon`
Run Phan in daemon mode.

```bash
# Start daemon
phan --daemon

# Request analysis
phan --daemon-request
```

For long-running processes with faster subsequent requests.

See [[Using-Phan-Daemon-Mode]] for details.

---

## Version and Help

### `--version` / `-v`
Display Phan version.

```bash
phan --version
```

### `--help` / `-h`
Display help message.

```bash
phan --help
```

### `--extended-help`
Display extended help with all options.

```bash
phan --extended-help
```

---

## Common Usage Patterns

### Pattern 1: Quick File Check

```bash
# Analyze specific file(s) without full project analysis
phan -n src/User.php
```

### Pattern 2: IDE Integration

```bash
# Fast analysis for IDE/editor
phan -n "$FILE_PATH" --output-mode json
```

### Pattern 3: CI/CD Pipeline

```bash
# Incremental analysis in CI
phan --incremental \
     --output-mode checkstyle \
     --output-file reports/phan.xml
```

### Pattern 4: Baseline Improvement

```bash
# Create baseline
phan --save-baseline .phan/.baseline.php

# Run with baseline (only NEW issues reported)
phan --load-baseline .phan/.baseline.php

# Fix some issues, then update baseline
phan --save-baseline .phan/.baseline.php
```

### Pattern 5: Performance Tuning

```bash
# For large codebases
phan --incremental \
     --processes 8 \
     --ast-trim-max-elements-per-level 512 \
     --max-union-type-set-size 2048
```

### Pattern 6: Parallel Processing

```bash
# Use all CPU cores
phan -j $(nproc)

# Specific number of processes
phan -j 4
```

---

## Exit Codes

```bash
# 0 - Analysis successful, no issues
phan; echo $?  # 0

# 1 - Analysis found issues
phan; echo $?  # 1

# 2 - Error during analysis
phan; echo $?  # 2
```

Use in scripts:

```bash
#!/bin/bash
phan
if [ $? -eq 0 ]; then
    echo "Analysis passed"
else
    echo "Analysis found issues"
    exit 1
fi
```

---

## Environment Variables

### `PHAN_ALLOW_MISSING_PARAM_TYPE`
Allow missing parameter type hints (v5 behavior).

```bash
PHAN_ALLOW_MISSING_PARAM_TYPE=1 phan
```

### `PHAN_DISABLE_XDEBUG`
Disable XDebug for performance.

```bash
PHAN_DISABLE_XDEBUG=1 phan
```

---

## Performance Monitoring

### Check Memory Usage

```bash
# Monitor peak memory
/usr/bin/time -v phan
```

### Measure Analysis Time

```bash
# Time the analysis
time phan
```

### Profile with Different Settings

```bash
#!/bin/bash
echo "Performance comparison"
echo "====================="

echo -e "\nStandard analysis:"
time phan

echo -e "\nIncremental analysis:"
time phan --incremental

echo -e "\nParallel (8 processes):"
time phan -j 8
```

---

## Troubleshooting

### "Configuration file not found"

```bash
# Specify config file explicitly
phan --config-file .phan/config.php

# Or use no config
phan -n file.php
```

### "Too many issues to display"

```bash
# Output to file instead
phan -f output.txt

# Or use quiet mode
phan -q
```

### "Analysis is slow"

```bash
# Use incremental analysis
phan --incremental

# Use parallel processing
phan -j 8

# Reduce memory/accuracy tradeoff
phan --ast-trim-max-elements-per-level 128
```

### "Inconsistent issues on different runs"

```bash
# Force full analysis
phan --force-full-analysis
```

---

## Complete Example: CI/CD Pipeline

```bash
#!/bin/bash
set -e

echo "=== Phan Static Analysis ==="

# Install dependencies
composer install

# Create baseline on first run
if [ ! -f .phan/.baseline.php ]; then
    echo "Creating baseline..."
    phan --save-baseline .phan/.baseline.php
fi

# Run analysis
echo "Running analysis..."
phan \
    --load-baseline .phan/.baseline.php \
    --incremental \
    --processes 8 \
    --output-mode checkstyle \
    --output-file reports/phan.xml

# Check results
if [ $? -eq 0 ]; then
    echo "✓ Analysis passed"
else
    echo "✗ Analysis found issues"
    exit 1
fi
```

---

## See Also

- [[Phan-Config-Settings]] - Configuration file reference
- [[Migrating-to-Phan-V6]] - V6 breaking changes
- [[Incremental-Analysis]] - Detailed incremental analysis guide
- [[Memory-and-Performance-Optimizations]] - Performance tuning
- [[Using-Phan-Daemon-Mode]] - Daemon mode details

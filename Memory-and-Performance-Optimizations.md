# Memory and Performance Optimizations

Phan v6 introduces significant performance improvements and memory optimization features to handle large codebases more efficiently. This guide explains the new optimization mechanisms and how to tune them for your project.

## Overview of V6 Performance Improvements

Phan v6 achieves **5-15% faster analysis** on large projects through:
- Large literal array summarization (reduces peak memory)
- Union type clamping (prevents runaway type growth)
- Internal stub caching (speeds up initialization)
- Conditional visitor optimization (skips unnecessary processing)
- Optional C extension integration (2-3x faster hashing)
- Incremental analysis support (10-50x faster re-runs)

## Large Literal Array Summarization

### Problem

Large literal arrays in code can cause significant memory usage during parsing. For example:

```php
$hugeArray = [
    'key1' => 'value1',
    'key2' => 'value2',
    // ... thousands of entries
];
```

Phan previously parsed and stored every element, leading to high peak memory consumption.

### Solution: AST Trimming (V6)

Phan v6 automatically summarizes large literal arrays to representative samples, drastically reducing memory usage.

```php
// .phan/config.php
return [
    // Maximum array elements per level (default: 256)
    'ast_trim_max_elements_per_level' => 256,

    // Maximum total elements across all levels (default: 512)
    'ast_trim_max_total_elements' => 512,
];
```

### Configuration Details

**ast_trim_max_elements_per_level** (default: 256)
- Limits the number of elements in a single array before summarization
- Arrays with more elements are reduced to a representative sample
- Example: 1000-element array → 256 representative elements

**ast_trim_max_total_elements** (default: 512)
- Cumulative limit across nested arrays
- Prevents deeply nested structures from consuming excessive memory
- Works in conjunction with per-level limits

### CLI Flags

```bash
# Override config temporarily
phan --ast-trim-max-elements-per-level 512
phan --ast-trim-max-total-elements 1024
```

### Example Output

```php
// Original
$config = [
    'database' => [
        'connections' => [
            'conn1' => [...1000 items...],
            'conn2' => [...1000 items...],
            // ... many more connections
        ]
    ]
];

// After AST Trimming
// Phan analyzes: representative sample of each level
// Memory usage: 5-10% of original
// Type accuracy: maintained for typical use cases
```

## Union Type Clamping

### Problem

Union types can grow unboundedly when merging arrays with different key types:

```php
// Type inference creates large unions
$merged = [];
foreach ($arrays as $array) {
    $merged = array_merge($merged, $array);
}
// Union type keeps growing with each merge
```

This can lead to:
- Exponential type growth
- High memory consumption
- Slow type comparisons

### Solution: Union Type Clamping (V6)

When union types exceed the configured threshold, Phan automatically clamps them while keeping type inference useful.

```php
// .phan/config.php
return [
    // Maximum union set size (default: 1024)
    'max_union_type_set_size' => 1024,
];
```

### Configuration

**max_union_type_set_size** (default: 1024)
- Maximum number of distinct types in a union
- When exceeded, types are intelligently merged
- Common types combined (e.g., `int|float` → `int|float`)
- Less common types combined into `mixed` when necessary

### CLI Flag

```bash
phan --max-union-type-set-size 2048
```

### Trade-offs

| Setting | Memory | Type Precision | Analysis Speed |
|---------|--------|-----------------|-----------------|
| 512     | Lower  | Lower           | Faster          |
| 1024    | Medium | Good (default)  | Medium          |
| 2048    | Higher | Better          | Slower          |
| 4096    | Very High | Excellent    | Slow            |

## C Extension Integration (phan_helpers)

### Overview

Phan can optionally use the `phan_helpers` C extension for 2-3x faster:
- AST node hashing
- Type deduplication
- Overall analysis on large projects (5-15% improvement)

### Installation

```bash
# Install the extension
pecl install phan_helpers

# Enable in php.ini
extension=phan_helpers.so

# Verify installation
php -m | grep phan_helpers
```

### Automatic Detection

Phan automatically detects and uses the extension if available. No configuration needed.

### Performance Metrics

With `phan_helpers` extension:
- Hash computation: 2-3x faster
- Type comparison: 1.5-2x faster
- Memory usage: 5-10% reduction
- Total analysis: 5-15% faster on large codebases

### When to Use

Use the C extension if:
- Analyzing projects > 100,000 lines of code
- Running Phan frequently in CI/CD
- Memory is limited
- Analysis time is critical

## Conditional Visitor Optimization

### Overview

Phan v6 skips visitor creation for simple if statements without else/elseif clauses, affecting ~60-70% of conditionals in typical code.

**Automatic benefit** (no configuration needed):
- Reduced memory usage
- Faster analysis
- Maintains accuracy for all control flow types

Example of optimized conditional:

```php
// Simple if - visitor skipped
if ($value > 0) {
    $positive = true;
}

// Complex if/else - visitor created
if ($value > 0) {
    $positive = true;
} else {
    $positive = false;
}
```

## Incremental Analysis

### Key Benefits

Incremental analysis (see [[Incremental-Analysis]]) provides dramatic speed improvements:

```bash
# First run: 45 seconds (full analysis)
phan --incremental

# Second run (no changes): 2-3 seconds
phan --incremental

# With one file changed: 5-10 seconds
phan --incremental
```

### Configuration

```php
// .phan/config.php
return [
    'use_incremental_analysis' => true,
    'incremental_analysis_file' => '.phan/.phan-incremental.php',
];
```

## Internal Stub Caching

### Overview

Phan v6 caches parsed internal PHP stub definitions between CodeBase instances. This provides significant speedup when:
- Running multiple analyses (testing frameworks)
- Daemon/server mode reloading
- Repeatedly analyzing in the same process

### Benefits

- First analysis: Parses stubs (~0.5s overhead)
- Subsequent analyses: Reuses cache (instant stub loading)
- Perfect for development workflows and testing

### Automatic

No configuration needed - this feature is always enabled.

## Optimization Tuning Guide

### Small Projects (< 50KB PHP code)

```php
return [
    // Disable incremental for simplicity
    'use_incremental_analysis' => false,

    // Standard settings
    'ast_trim_max_elements_per_level' => 256,
    'max_union_type_set_size' => 1024,
];
```

**Expected performance**: < 1 second

### Medium Projects (50KB - 10MB PHP code)

```php
return [
    // Enable incremental analysis
    'use_incremental_analysis' => true,

    // Standard settings
    'ast_trim_max_elements_per_level' => 256,
    'ast_trim_max_total_elements' => 512,
    'max_union_type_set_size' => 1024,
];
```

**Expected performance**:
- First run: 10-30 seconds
- Incremental runs: 1-5 seconds

### Large Projects (> 10MB PHP code)

```php
return [
    // Enable incremental analysis
    'use_incremental_analysis' => true,

    // Increase limits for complex code
    'ast_trim_max_elements_per_level' => 512,
    'ast_trim_max_total_elements' => 1024,
    'max_union_type_set_size' => 2048,

    // Other performance settings
    'parallel_tools' => true,
    'processes' => 8,  // Adjust for your CPU count
];
```

**Expected performance**:
- First run: 30-120 seconds
- Incremental runs: 5-30 seconds

**Recommended**: Install phan_helpers C extension for additional 5-15% speedup.

## Memory Monitoring

### Check Memory Usage

```bash
# Run Phan and display memory statistics
phan 2>&1 | tail -20

# Monitor peak memory usage
/usr/bin/time -v phan
```

### Interpreting Results

```
Maximum resident set size (kbytes): 512000
```

Typical memory usage:
- Small projects: 50-100 MB
- Medium projects: 200-500 MB
- Large projects: 1-5 GB (with optimization)

## Performance Benchmarking

### Before Optimization

```bash
# Test without optimization
mv .phan/.phan-incremental.php .phan/.phan-incremental.php.bak

time phan
# real    0m45.234s
# user    2m15.890s
# sys     0m8.234s
```

### After Optimization

```bash
# Test with incremental analysis enabled
time phan -i
# real    0m3.456s  (13x faster!)
# user    0m12.345s
# sys     0m1.234s
```

## Common Performance Issues and Solutions

### Issue: High Memory Usage on Large Arrays

**Symptom**: Memory limit exceeded on codebases with large config arrays

**Solution**:
```php
// Reduce trimming thresholds
'ast_trim_max_elements_per_level' => 128,
'ast_trim_max_total_elements' => 256,
```

### Issue: Slow Analysis on First Run

**Symptom**: First run of Phan takes very long

**Solution**:
```bash
# Ensure phan_helpers is installed for 5-15% speedup
pecl install phan_helpers

# Or use parallel processing
phan --processes 8
```

### Issue: Union Types Too Complex

**Symptom**: `PhanTypeMismatchUnionTooComplex` warnings

**Solution**:
```php
// Increase union type limit
'max_union_type_set_size' => 2048,
```

## Monitoring Performance Across Versions

```bash
#!/bin/bash
# Compare performance between runs

echo "Phan v6 Performance Baseline"
echo "=============================="

for run in {1..3}; do
    echo "Run $run:"
    time phan -i > /dev/null 2>&1
    echo ""
done
```

## Further Reading

- [[Incremental-Analysis]] - Detailed guide on incremental analysis
- [[Migrating-to-Phan-V6]] - V6 migration guide
- [[Using-Phan-From-Command-Line]] - CLI reference
- [Phan Issues](https://github.com/phan/phan/issues) - Report performance issues

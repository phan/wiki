# Phan Helpers Extension

The `phan_helpers` extension is an optional PHP C extension that significantly accelerates Phan static analysis by providing optimized implementations of performance-critical functions.

**Repository**: https://github.com/phan/phan_helpers

**Target Version**: Phan v6+

## Overview

Phan's most time-consuming operations are often in the type system - deduplicating types, computing union type identifiers, generating cache keys, and hashing AST nodes. The `phan_helpers` extension provides native C implementations of these operations, typically delivering **2-3x performance improvements** and **5-15% overall analysis speedup** on large projects.

## Installation

### Prerequisites

- PHP 8.1+ development headers
- Autotools (autoconf, automake, libtool)
- A C compiler (gcc, clang)

### Build from Source

```bash
cd ~/src/phan_helpers  # or clone from github.com/phan/phan_helpers

phpize
./configure
make
sudo make install
```

### Enable in php.ini

Add the extension to your `php.ini`:

```ini
extension=phan_helpers.so
```

On some systems, you may need the full path:

```ini
extension=/usr/lib/php/extensions/no-debug-non-zts-20230718/phan_helpers.so
```

### Verify Installation

```bash
php --ri phan_helpers
```

You should see:

```
phan_helpers

phan_helpers support => enabled
```

## Functions

The extension provides four optimized functions. Phan automatically detects their availability and uses them when present.

### `phan_unique_types(array $type_list): array`

Fast deduplication of Type objects using handle-based hashing.

**What it does**:
Removes duplicate Type objects from an array. Phan does this constantly when merging types from different code paths.

**Performance**:
- O(n) instead of O(n²) for small arrays
- ~2-3x faster than PHP implementation

**Example in Phan source**:
```php
// Phan's UnionType class uses this internally
if (function_exists('phan_unique_types')) {
    $unique_types = phan_unique_types($type_list);
} else {
    $unique_types = UnionType::getUniqueTypes($type_list);
}
```

### `phan_unique_union_id(array $type_list, array $real_type_list = []): string`

Compute the canonical identifier for a union type.

**What it does**:
Creates a unique string ID for a union of types. This ID is used for caching array shapes and template substitutions.

**Performance**:
- Eliminates repeated `spl_object_id()` calls
- Uses native C sorting on type identifiers
- Avoids temporary PHP arrays during string concatenation

**Why it matters**:
Every time Phan encounters a union type, it needs to generate a cache key. This happens millions of times in large projects.

### `phan_array_shape_cache_key(array $field_types, bool $is_nullable): string`

Generate the stable cache key for array shape types.

**What it does**:
Creates a deterministic key for caching inferred array shapes like `array{id: int, name: string}`.

**Performance**:
- Avoids `serialize()` calls
- No temporary array allocations
- Direct C string building

**Use case**:
When Phan infers the shape of an array from assignments, it caches this information. This function generates the cache key without allocating intermediate PHP objects.

### `phan_ast_hash(Node|string|int|float|null $node): string`

Fast XXH3-128 hashing of AST nodes for duplicate detection.

**What it does**:
Creates a cryptographic hash of an AST node structure. Used to detect when code has been written identically multiple times.

**Performance**:
- 2-3x faster than PHP MD5-based implementation
- Uses XXH3-128 (modern, faster, better distribution than MD5)

**Example**:
```php
// Detect duplicate code blocks
$hash1 = phan_ast_hash($node1);
$hash2 = phan_ast_hash($node2);

if ($hash1 === $hash2) {
    // Nodes are semantically identical
}
```

## Impact on Analysis Speed

With all functions available and active:

| Codebase Size | Improvement | Notes |
|---|---|---|
| Small (<100k lines) | 5-10% | Less noticeable; type deduplication less frequent |
| Medium (100k-500k lines) | 10-15% | Most projects see benefits here |
| Large (>500k lines) | 15-25% | Especially with complex type unions |

The extension has virtually no overhead when Phan isn't running, as it's only loaded when explicitly enabled in `php.ini`.

## Automatic Detection

Phan v6+ automatically detects the extension:

```bash
phan
# Uses phan_helpers.so if available
```

You don't need to change your Phan configuration. The extension is detected at runtime and used transparently.

## Building with Debug Symbols

For development or debugging:

```bash
phpize
./configure --enable-debug
make
```

## Troubleshooting

### Extension loads but not being used

Verify it's actually enabled:

```bash
php -m | grep phan_helpers
```

Check that the extension is listed in `php -i`:

```bash
php -i | grep phan_helpers
```

### Compilation failures

Ensure you have PHP development headers:

```bash
# On Ubuntu/Debian
sudo apt-get install php8.3-dev

# On macOS with Homebrew
brew install php@8.3

# On Fedora/RHEL
sudo dnf install php-devel
```

### Performance not improved

If you don't see improvement:

1. Verify the extension is loaded: `php --ri phan_helpers`
2. Run a fresh analysis: `phan --force-full-analysis`
3. Very small projects may not benefit much from the extension

## Benchmarking

Time a full analysis with and without the extension:

```bash
# Disable extension temporarily
php -d extension_loaded=0 -d disable_functions=phan_unique_types,phan_unique_union_id,phan_array_shape_cache_key,phan_ast_hash /usr/local/bin/phan > /dev/null

# Re-enable and time
php /usr/local/bin/phan > /dev/null
```

Or use the built-in benchmark script:

```bash
php src/phan_helpers/benchmark.php
```

## Platform Support

The extension is tested on:
- Linux (x86-64, ARM64)
- macOS (Intel, Apple Silicon)
- Windows (with appropriate MSVC build setup)

## See Also

- [[Memory-and-Performance-Optimizations]] - Other performance tuning options
- [[Using-Phan-From-Command-Line]] - CLI options including parallel processing (`-j`)
- [[Incremental-Analysis]] - Incremental analysis for faster re-runs

# Phan Wiki Scripts

## add_demo_links.php

Automatically adds "Try this in Phan-in-Browser" links to markdown documentation files containing PHP code examples.

### Usage

```bash
php add_demo_links.php <input.md> [output.md]
```

**Examples:**

```bash
# Output to stdout (preview)
php add_demo_links.php ../v6-Generic-Types.md

# Write to a new file
php add_demo_links.php ../v6-Generic-Types.md ../v6-Generic-Types-new.md

# Update file in place (be careful!)
php add_demo_links.php ../v6-Generic-Types.md /tmp/output.md
mv /tmp/output.md ../v6-Generic-Types.md
```

### How It Works

1. Parses markdown files looking for PHP code blocks (```php ... ```)
2. Extracts the PHP code from each block
3. Compresses the code using LZ-String compression
4. Generates a Phan-in-Browser URL with the compressed code
5. Adds a clickable link below each code block

### Features

- **Self-contained**: Embeds LZ-String compression library (no composer needed)
- **Idempotent**: Running the script multiple times produces the same output
- **Smart link replacement**: Automatically replaces outdated links with fresh ones
- **Preserves formatting**: Maintains original markdown structure
- **Universal PHP examples**: All links point to PHP 8.4 + Phan v6-dev + ast 1.1.3

### Testing

Test the LZ compression with the "Hello World" example:

```bash
TEST=1 php add_demo_links.php dummy
```

Expected output should match:
```
c=DwfgDgFmBQCmDGED2ACARACVgG26g6kgE7YAmaA3EA
```

### Implementation Details

The script embeds a complete PHP implementation of LZ-String compression from:
https://github.com/nullpunkt/lz-string-php

This ensures the script works standalone without requiring `composer install`.

### URL Format

Generated URLs follow this pattern:
```
https://phan.github.io/demo/?c={compressed-code}&php=84&phan=v6-dev&ast=1.1.3
```

Where:
- `c` = LZ-compressed PHP code
- `php` = PHP version (84 = 8.4)
- `phan` = Phan version
- `ast` = php-ast extension version

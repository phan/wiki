# Phan Wiki Scripts

## add_demo_links.php

Automatically adds "Try this in Phan-in-Browser" links to markdown documentation files containing PHP code examples.

### Usage

```bash
php add_demo_links.php [--php=84] [--phan=v6-dev] [--ast=1.1.3] <input.md> [output.md]
```

**Examples:**

```bash
# Output to stdout (preview); a summary of added/refreshed/removed links goes to stderr
php add_demo_links.php ../Generic-Types-V6.md

# Write to a file, then replace the original
php add_demo_links.php ../Generic-Types-V6.md /tmp/output.md
mv /tmp/output.md ../Generic-Types-V6.md

# Point every link on a page at PHP 8.5
php add_demo_links.php --php=85 ../PHP-8.4-8.5-Support.md /tmp/output.md
```

### Per-block markers

Put an HTML comment on the line before a ` ```php ` fence (blank lines in between are fine). GitHub does not render it.

```markdown
<!-- demo: skip -->
<!-- demo: php=85 -->
<!-- demo: php=86 phan=v6-dev ast=1.1.3 -->
```

- `skip`: no link for this block. An existing link after it is removed.
- `php=`, `phan=`, `ast=`: versions for this block only.

### Version precedence

For each block, highest first:

1. Per-block marker
2. `--php` / `--phan` / `--ast` on the command line
3. The versions in the block's existing link, so a refresh keeps them
4. Built-in defaults: `php=84`, `phan=v6-dev`, `ast=1.1.3`

Valid values are whatever https://phan.github.io/demo/manifest.json lists. The demo ignores a version it doesn't know and uses its own default.

### How It Works

1. Finds PHP code blocks (` ```php ... ``` `)
2. Compresses each block's code with LZ-String, adding `<?php` if missing
3. Writes a `[▶](url "Try this example in Phan-in-Browser")` link below the block
4. Replaces any existing demo link after the block, in either the current `[▶](...)` format or the older `**[Try this ...](...)**` format, including duplicate links left by older versions of this script

Running it again on its own output changes nothing. Links placed by hand anywhere other than directly after a block are left alone.

### Testing

Test the LZ compression with the "Hello World" example:

```bash
TEST=1 php add_demo_links.php
```

It prints `OK` and exits 0 if the compressed output is `c=DwfgDgFmBQCmDGED2ACARACVgG26g6kgE7YAmaA3EA`, otherwise `FAIL` and exits 1.

### Implementation Details

The script embeds a complete PHP implementation of LZ-String compression from:
https://github.com/nullpunkt/lz-string-php

This ensures the script works standalone without requiring `composer install`.

### URL Format

Generated URLs follow this pattern:
```
https://phan.github.io/demo/?c={compressed-code}&php={php}&phan={phan}&ast={ast}
```

Where:
- `c` = LZ-compressed PHP code
- `php` = PHP version (84 = 8.4, 86 = 8.6)
- `phan` = Phan version
- `ast` = php-ast extension version

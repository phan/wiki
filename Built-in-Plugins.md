# Built-in Plugins

Phan ships with over 50 built-in plugins that add optional checks beyond the core analysis. Plugins are disabled by default — you enable the ones that make sense for your project.

## Enabling Plugins

Add plugin names to the `plugins` array in `.phan/config.php`:

```php
'plugins' => [
    'AlwaysReturnPlugin',
    'DuplicateArrayKeyPlugin',
    'PregRegexCheckerPlugin',
    'UnreachableCodePlugin',
    'UseReturnValuePlugin',
],
```

Each entry is either a built-in plugin name (string) or a path to a custom plugin file.

To write your own plugins, see [[Writing Plugins for Phan]].

---

## Recommended Starter Set

These plugins are broadly useful, low-noise, and safe to enable in most projects:

| Plugin | What it catches |
|--------|----------------|
| `AlwaysReturnPlugin` | Functions/methods that don't always return a value but lack `void` return type |
| `DuplicateArrayKeyPlugin` | Duplicate or equivalent array keys; arrays mixing `key => value` with positional values |
| `EmptyStatementListPlugin` | Empty `if`/`for`/`foreach`/`while` bodies (likely bugs) |
| `LoopVariableReusePlugin` | Loop variables reused across iterations without reset |
| `PregRegexCheckerPlugin` | Invalid PCRE regex patterns passed to `preg_*()` functions |
| `PrintfCheckerPlugin` | Mismatched format strings in `printf`, `sprintf`, `sscanf`, etc. |
| `UnreachableCodePlugin` | Syntactically unreachable statements after `return`/`throw`/`die` |
| `UseReturnValuePlugin` | Calls to functions whose return value is always used but is being discarded |

---

## Plugin Reference

### Code Quality

| Plugin | Description | Notes |
|--------|-------------|-------|
| `AlwaysReturnPlugin` | Warns when a function or method may not return a value but has no `void` return type | Complements Phan's built-in return type checks |
| `AddNeverReturnTypePlugin` | Warns when a function or method never returns (infinite loop or always throws) but lacks the `never` return type | Useful when migrating to PHP 8.1+ |
| `DuplicateArrayKeyPlugin` | Detects duplicate or equivalent keys in array literals and duplicate case values in switch statements | Also warns when mixing `key => value` entries with plain positional values |
| `DuplicateExpressionPlugin` | Detects identical expressions in binary operations (e.g. `$x === $x`, `$a || $a`) | |
| `EmptyStatementListPlugin` | Detects empty statement bodies in `if`, `for`, `foreach`, `while`, `switch`, `try` | Deliberate empty bodies can be marked with a comment; configure via `plugin_config['empty_statement_list_ignore_todos']` |
| `EmptyMethodAndFunctionPlugin` | Warns about functions and methods with entirely empty bodies | Useful for finding unimplemented stubs |
| `LoopVariableReusePlugin` | Warns when a loop variable (`$i`, `$key`, etc.) is used after the loop body without being reset | |
| `RedundantAssignmentPlugin` | Detects assignments where the variable already has the assigned value | E.g. `$result = false; if (...) { $result = false; }` |
| `ConstantVariablePlugin` | Detects variables whose value never changes and could be replaced with a constant or literal | High false positive rate; use `constant_variable_detection` config option instead |
| `SimplifyExpressionPlugin` | Suggests simplifications for expressions that are more complex than needed | E.g. `$x > 0 ? true : false` → `$x > 0` |
| `UnreachableCodePlugin` | Detects syntactically unreachable code after `return`, `throw`, `die`, `exit`, `continue`, `break` | |

### Type Safety

| Plugin | Description | Notes |
|--------|-------------|-------|
| `NonBoolBranchPlugin` | Warns when a non-boolean expression is used as a branch condition | High false positive rate; best for strictly-typed codebases |
| `NonBoolInLogicalArithPlugin` | Warns when non-boolean values are used with `&&`, `\|\|`, `xor` operators | High false positive rate |
| `NumericalComparisonPlugin` | Warns when numeric values are compared with `==`/`!=` instead of `===`/`!==` | Use `StrictLiteralComparisonPlugin` for string literals |
| `StrictComparisonPlugin` | Warns about `in_array()` calls without the `$strict = true` parameter | |
| `StrictLiteralComparisonPlugin` | Warns when string literals are compared with loose equality (`==`/`!=`) | High false positive rate; not suitable for all codebases |
| `UnknownElementTypePlugin` | Warns when functions, methods, or properties lack type declarations | Useful for gradually adding type annotations to a codebase |
| `UnknownClassElementAccessPlugin` | Warns when a method call or property access is on a type that Phan couldn't fully resolve | |
| `SuspiciousParamOrderPlugin` | Warns when function arguments appear to be passed in the wrong order based on parameter names | Heuristic; may have false positives |
| `UncoveredEnumCasesInMatchPlugin` | Warns when a `match` expression on an enum or boolean doesn't cover all cases | PHP 8.1+ |

### Security & Unsafe Code

| Plugin | Description | Notes |
|--------|-------------|-------|
| `UnsafeCodePlugin` | Warns about inherently unsafe constructs: `eval()`, `shell_exec()`, backtick operator, `create_function()` | |
| `NoAssertPlugin` | Warns about `assert()` usage | |
| `RemoveDebugStatementPlugin` | Warns about debugging statements left in code: `var_dump()`, `print_r()`, `var_export()`, `debug_backtrace()` | |

### PHPDoc Quality

| Plugin | Description | Notes |
|--------|-------------|-------|
| `HasPHPDocPlugin` | Warns when public methods, functions, or properties lack a doc comment | Configurable: `plugin_config['has_phpdoc_method_ignore_regex']` to skip methods matching a regex; `has_phpdoc_check_duplicates` to also flag duplicate doc comments |
| `PHPDocInWrongCommentPlugin` | Warns when PHPDoc annotations like `@param` or `@return` appear in `/*` or `//` comments instead of `/**` doc comments | Catches a common mistake |
| `PHPDocRedundantPlugin` | Warns when a doc comment only contains `@param`/`@return` annotations that exactly match the function signature and add no information | Supports `--automatic-fix` |
| `PHPDocToRealTypesPlugin` | Suggests converting PHPDoc-only types to native PHP type declarations | E.g. suggests adding `: string` return type when `@return string` is already present. Supports `--automatic-fix` |
| `MoreSpecificElementTypePlugin` | Warns when a return type could be made more specific based on Phan's inferred type | E.g. `@return object` when Phan infers `ArrayObject` |

### Code Style

| Plugin | Description | Notes |
|--------|-------------|-------|
| `WhitespacePlugin` | Warns about tabs, Windows line endings (`\r\n`), and trailing whitespace | Supports `--automatic-fix` to clean up whitespace |
| `ShortArrayPlugin` | Warns about `array()` syntax; suggests `[]` instead | |
| `InlineHTMLPlugin` | Warns about accidental inline HTML or whitespace outside PHP tags | Useful for library code that should never output HTML |
| `NotFullyQualifiedUsagePlugin` | Warns when global functions or constants are used without a leading `\` in namespaced code | Avoids a runtime lookup; slight performance win |
| `PreferNamespaceUsePlugin` | Warns when a fully-qualified class name is used where a `use` statement already exists | |
| `CaseMismatchPlugin` | Warns when a function or class is called with different casing than its declaration | Catches likely typos |
| `DollarDollarPlugin` | Warns about variable variables (`$$var`) | Hard to statically analyze |

### PHPUnit Support

| Plugin | Description | Notes |
|--------|-------------|-------|
| `PHPUnitAssertionPlugin` | Marks PHPUnit assertion methods (`assertTrue`, `assertNull`, etc.) as having side effects so Phan doesn't flag them as unused return values | |
| `PHPUnitNotDeadCodePlugin` | Marks PHPUnit test methods (those starting with `test`) and methods using `@test` as referenced | Prevents false positive dead code warnings for test methods |

### Methods & Properties

| Plugin | Description | Notes |
|--------|-------------|-------|
| `PossiblyStaticMethodPlugin` | Warns when a method could be declared `static` because it doesn't use `$this` | |
| `AvoidableGetterPlugin` | Warns when `$this->getProperty()` is called inside the same class where `$this->property` would work directly | |

### Specialized

| Plugin | Description | Notes |
|--------|-------------|-------|
| `PregRegexCheckerPlugin` | Validates PCRE regex patterns in `preg_match()`, `preg_replace()`, etc. | Tests patterns against an empty string to catch syntax errors |
| `PrintfCheckerPlugin` | Validates `printf`/`sprintf`/`sscanf`/`vprintf` format strings against their arguments | Checks argument count, types, and format specifier validity |
| `SleepCheckerPlugin` | Validates `__sleep()` return values | Checks that returned property names actually exist on the class |
| `FFIAnalysisPlugin` | Adds type handling for `FFI\CData` variables | Required for projects using PHP's FFI extension |
| `InvalidVariableIssetPlugin` | Warns when `isset()` is called on a variable that Phan can determine is definitely undeclared | |
| `StaticVariableMisusePlugin` | Warns about potentially misused `static` variables | |
| `DuplicateConstantPlugin` | Warns about constant declarations that duplicate an existing constant | |
| `AddNeverReturnTypePlugin` | Suggests adding `never` return type to functions that never return | PHP 8.1+ |
| `DeprecateAliasPlugin` | Warns about use of deprecated PHP function aliases (e.g. `pos()` instead of `current()`) | |
| `AsymmetricVisibilityPlugin` | Checks usage of asymmetric visibility (`public private(set)`) properties | PHP 8.4+ |
| `UnusedSuppressionPlugin` | Warns when a `@suppress` or `@phan-suppress-next-line` annotation is no longer suppressing anything | **Requires single-process mode** (`'processes' => 1`) |

---

## Plugin-specific Configuration

Some plugins accept configuration via the `plugin_config` key in `.phan/config.php`:

```php
'plugin_config' => [
    // HasPHPDocPlugin: skip methods matching this regex
    'has_phpdoc_method_ignore_regex' => '@^(get|set)[A-Z]@',
    // HasPHPDocPlugin: also check for duplicate doc comments
    'has_phpdoc_check_duplicates' => true,

    // EmptyStatementListPlugin: treat // TODO comments as intentional
    'empty_statement_list_ignore_todos' => true,

    // UseReturnValuePlugin options (see plugin source for full list)
    'use_return_value_verbose' => false,
],
```

Refer to each plugin's source in [`.phan/plugins/`](https://github.com/phan/phan/tree/v6/.phan/plugins) for a full list of supported `plugin_config` keys.

---

## Performance Notes

- Most plugins run inside the per-file worker processes and add negligible overhead.
- **`UnusedSuppressionPlugin`** must run in single-process mode (`'processes' => 1`) because it needs to see all issues before deciding which suppressions are unused. Using it with `--processes N` (N > 1) will produce incorrect results.
- **`PHPDocToRealTypesPlugin`** and **`PHPDocRedundantPlugin`** support `--automatic-fix`, which requires `--force-polyfill-parser` (or `--force-polyfill-parser-with-original-tokens`).
- Enabling many node-visitor plugins (those implementing `PostAnalyzeNodeCapability`) is generally fine — Phan dispatches them in a single AST pass.

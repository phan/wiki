# Phan V5 Documentation Archive

This page archives documentation written for Phan v5, which is now in maintenance mode (critical bug fixes only). These pages have been superseded by updated v6 versions.

If you are still running Phan v5, these pages remain valid. If you are upgrading, see **[[Migrating to Phan V6|Migrating-to-Phan-V6]]**.

---

## Annotating Your Source Code (v5)

**[[Annotating Your Source Code]]** — The original annotation guide covering `@var`, `@param`, `@return`, `@method`, and suppression annotations. Superseded by **[[Annotating Your Source Code V6|Annotating-Your-Source-Code-V6]]**, which adds variance annotations, utility types, PHP 8.4/8.5 features, and interactive examples.

## Generic Types (v5)

**[[Generic Types]]** — The original generics guide covering `@template`, basic generic classes, and function templates. Superseded by **[[Generic Types V6|Generic-Types-V6]]**, which adds generic interfaces, generic traits, template constraints, variance, utility types (`key-of<T>`, `value-of<T>`, `int-range`), and many more features.

## Legacy Plugin API (v1/v2)

**[[Writing Legacy Plugins for Phan]]** — Documents the v1 and v2 plugin APIs. Support for v1 plugins was removed in Phan 1.0.0. V2 plugins were deprecated in Phan 2.0.0 in favor of PluginV3. For current plugin development, see **[[Writing Plugins for Phan]]**.

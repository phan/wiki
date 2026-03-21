# Phan — Static Analyzer for PHP

> Phan finds bugs in PHP code before you run it. It proves incorrectness rather than correctness, giving you a comprehensive understanding of your type system with minimal false positives.

```bash
composer require --dev phan/phan
./vendor/bin/phan --init   # generates .phan/config.php
./vendor/bin/phan          # analyzes your project
```

[**Try Phan in your browser →**](https://phan.github.io/demo/) &nbsp;|&nbsp; [GitHub](https://github.com/phan/phan) &nbsp;|&nbsp; [Releases](https://github.com/phan/phan/releases)

---

## What Phan checks

| Type safety | Code quality | PHP compatibility |
|---|---|---|
| Union types, generics, array shapes | Undefined variables, dead code | PHP 8.1 – 8.5 features |
| Nullability, type narrowing | Redundant conditions, unreachable code | Property hooks, pipe operator |
| Template constraints, variance | Missing return types, wrong parameter counts | Deprecated attributes |

---

## 🚀 Getting Started

**[[Getting Started]]**
Install via Composer, generate a config, and run your first analysis. Includes GitHub Actions and GitLab CI examples.

**[[Tutorial for Analyzing a Large Sloppy Code Base]]**
A practical walkthrough for introducing Phan incrementally into an existing project without being overwhelmed by warnings.

**[[What Static Analysis Lets You Do]]**
An overview of the kinds of bugs static analysis catches that tests and code review miss.

**[[Frequently Asked Questions]]**
Common issues when getting started, and how to resolve them.

---

## ✍️ Annotating Your Code

**[[Annotating Your Source Code V6|Annotating-Your-Source-Code-V6]]**
The complete guide to PHPDoc annotations in Phan v6: `@param`, `@return`, `@var`, union types, generic types, variance, utility types, assertion annotations, PHP 8.4/8.5 features, and suppression. Includes interactive runnable examples.

**[[About Union Types]]**
What union types are, how Phan represents them internally, and how they flow through your code.

**[[Generic Types V6|Generic-Types-V6]]**
Using `@template` for generic classes, interfaces, traits, and functions. Template constraints, variance annotations, and utility types (`key-of<T>`, `value-of<T>`, `int-range`, and more).

**[[Advanced Type System in V6|Advanced-Type-System-V6]]**
Intersection types with unknown classes, improved type narrowing, array inference, and union type clamping.

**[[Typing Parameters]]**
How Phan handles the interaction between `@param` annotations and native parameter type hints.

---

## ⚙️ Configuration & CLI

**[[Phan Config Settings]]**
Complete reference for all Phan configuration options: file targeting, issue filters, analysis strictness, output formats, and performance tuning.

**[[Using Phan From the Command Line|Using-Phan-From-Command-Line]]**
All CLI flags and options for Phan v6, including incremental analysis flags and the `-n` (no config) mode.

**[[How To Use Stubs]]**
Setting up a `stubs/` directory to make external code (libraries, extensions) available to Phan without adding it to `directory_list`.

**[[Externally Generating a List of Files for Phan|Externally-generating-a-list-of-files-for-Phan]]**
Controlling exactly which files Phan analyzes using shell scripts or build tools.

---

## 🔌 Plugins

**[[Built-in Plugins|Built-in-Plugins]]**
Reference for all 50+ built-in plugins: what each one checks, how to enable it, recommended starter sets, and plugin-specific configuration options.

**[[Writing Plugins for Phan]]**
Creating custom analysis plugins using the PluginV3 API: all 20 capability interfaces, visitors, issue emission, and implementing `--automatic-fix` support.

---

## 🔍 Analysis & Issues

**[[Incrementally Strengthening Analysis]]**
A step-by-step guide to ratcheting up Phan's strictness over time without drowning in warnings.

**[[Issue Types Reference|Issue-Types-Reference]]**
Complete reference for all Phan issue types, organized by category, with examples and remediation guidance.

**[[Tooling and Suppression Baselines|Tooling-and-Suppression-Baseline]]**
Generating a baseline file to suppress existing issues, batch-adding suppressions, and managing suppressions over time.

---

## ⚡ Performance

**[[Incremental Analysis|Incremental-Analysis]]**
Re-analyze only changed files and their dependents — 10–50× faster than a full run on large projects.

**[[Speeding up Phan Analysis]]**
Configuration and workflow tips for faster analysis: parallelism, file exclusions, polyfill parser, and more.

**[[Memory and Performance Optimizations|Memory-and-Performance-Optimizations]]**
AST trimming, union type clamping, and tuning Phan for very large codebases.

**[[Phan Helpers Extension|Phan-Helpers-Extension]]**
Optional C extension providing 2–3× faster AST hashing and type deduplication, with minimal configuration required.

**[[Different Issue Sets On Different Numbers of CPUs]]**
Why parallel analysis can produce slightly different results, and how to get consistent output.

---

## 🖥 Editor & IDE Support

**[[Editor Support]]**
Setting up Phan in VS Code, Vim, Emacs, and other editors. Includes how to write editor integrations using the language server protocol.

**[[Using Phan Daemon Mode]]**
Low-latency single-file analysis for editor integrations. Phan stays running in the background and responds to file analysis requests.

---

## 🐘 PHP Version Support

**[[PHP 8.4 and 8.5 Support|PHP-8.4-8.5-Support]]**
Phan's support for property hooks, `#[Deprecated]`, `#[NoDiscard]`, typed class constants, and the PHP 8.5 pipe operator.

**[[Migrating to Phan V6|Migrating-to-Phan-V6]]**
Breaking changes, removed config options, and CLI flag changes when upgrading from Phan v5 to v6.

---

## 🛠 Developers

**[[Developer's Guide To Phan]]**
Architecture overview, how to set up a development environment, and how the analysis pipeline works.

**[[Writing Plugins for Phan]]**
Creating custom analysis plugins using the PluginV3 API: visitors, capabilities, issue emission, and autofix support.

**[[How To Create a Phan Release]]**
Checklist for cutting a new Phan release: version bump, changelog, phar build, signing, and publishing.

---

## Phan v5 Documentation

Looking for older documentation? Phan v5 is in maintenance mode (critical bug fixes only). The pages below still exist but have been superseded by v6 versions.

**[[Phan V5 Documentation|Phan-V5-Documentation]]** — Archived v5 annotation guide, generic types guide, and legacy plugin API docs.

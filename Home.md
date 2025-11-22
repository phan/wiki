# Using Phan

## Setting up Phan

**[[Getting Started]]**<br />
Start here for getting Phan installed and running on your code base.

**[[Annotating Your Source Code]]** | **[[Annotating Your Source Code V6|Annotating-Your-Source-Code-V6]]**<br />
This document describes how to add types to your code via doc block annotations. (e.g. `@param`, `@return`, `@var`, `@suppress`)

**[[About Union Types]]**<br />
An explanation of what union types are and how to use them.

**[[Tutorial for Analyzing a Large Sloppy Code Base]]**<br/>
A tutorial providing some guidance on how to get started analyzing your code and get to a place where static analysis is actually useful.

## Improving Phan Analysis

**[[Incrementally Strengthening Analysis]]**<br />
A guide to how Phan can be configured to slowly increase the strength of the analysis.

- [Adding Phan plugins to your project](https://github.com/phan/phan#features-provided-by-plugins) can strengthen analysis in other ways.

**[[Issue Types Reference|Issue-Types-Reference]]**<br />
Complete reference of all major Phan issue types, organized by category, with examples and how to fix each one.

**[[Phan Config Settings]]**<br />
Documents Phan's config settings. These affect the strictness of analysis, what files are analyzed, etc.

**[[Typing Parameters]]**<br />
A guide to handling interactions between Phan's `@param` types and declared parameter types or type-hints.

**[[How To Use Stubs]]**<br />
Details on setting up a `stubs` directory for making code available to Phan that isn't loaded on its runtime.

**[[Speeding up Phan Analysis]]**<br/>
A list of suggestions that may help speed up Phan analysis on your project.

## Using Advanced Features

**[[Generic-Types-V6]]**<br/>
Phan has primordial support for generic (templated) classes via type the annotations `@template` and `@inherits` and via a type syntax of the form `MyClass<T>` that may be referenced within doc-block annotations.

**[[Editor Support]]**<br/>
This article explains how to set up support for Phan in an editor/IDE.
Vim, Emacs, and VS Code have plugins/extensions written for them. This article also explains how to create plugins/extensions for an editor.

**[[Using Phan Daemon Mode]]**<br/>
Daemon mode lets you request Phan results from your editor or IDE to detect Phan issues for a single file, with much lower latency than a full analysis.

## Phan V6 New Features and Migration

**[[Migrating to Phan V6|Migrating-to-Phan-V6]]**<br/>
Breaking changes, removed configuration options, and migration guidance for upgrading from Phan v5 to v6.

**[[PHP 8.4 and 8.5 Support|PHP-8.4-8.5-Support]]**<br/>
Full support for PHP 8.4 property hooks, `#[Deprecated]` attribute, typed class constants, and PHP 8.5 pipe operator with `#[NoDiscard]` attribute.

**[[Incremental Analysis|Incremental-Analysis]]**<br/>
Significantly faster re-analysis of changed files. Only re-analyzes modified files and dependents, achieving 10-50x speedup on large projects.

**[[Memory and Performance Optimizations|Memory-and-Performance-Optimizations]]**<br/>
Optimize Phan for large codebases using AST trimming, union type clamping, C extension integration, and configuration tuning.

**[[Tooling and Suppression Baselines|Tooling-and-Suppression-Baseline]]**<br/>
Symbol-based suppressions, baseline file generation, and the new `add_suppressions.php` tool for batch suppression management.

**[[Using Phan From the Command Line|Using-Phan-From-Command-Line]]**<br/>
Complete reference for Phan v6 CLI flags, options, and usage patterns including the new `-n` behavior and incremental analysis flags.

**[[Advanced Type System in V6|Advanced-Type-System-V6]]**<br/>
Improved type narrowing, array inference, intersection types with unknown classes, and union type clamping details.

## Performance and Tools

**[[Phan Helpers Extension|Phan-Helpers-Extension]]**<br/>
Optional PHP C extension for 2-3x faster AST hashing and type deduplication. Provides automatic performance boost for large projects with minimal configuration.

**[[Phan Config Settings]]**<br />
Documents Phan's config settings. These affect the strictness of analysis, what files are analyzed, performance tuning options, etc.

## Frequently Asked Questions

**[[Frequently Asked Questions]]**<br/>
A document with answers to common issues encountered when getting started with Phan.

**[[Different Issue Sets On Different Numbers of CPUs]]**<br/>
A document describing issues that may be seen when running Phan on more than one core, and workarounds.

**[[What Static Analysis Lets You Do]]**<br/>
A document describing various benefits of running a static analyzer on your code.

# Developers' Guides

**[[Developer's Guide To Phan]]**<br />
A guide for developers looking to hack on Phan.

**[[Writing Plugins for Phan]]**<br />
A guide to writing one-off plugins for Phan.

**[[How To Create a Phan Release]]**<br />
Follow this checklist when creating a new release of Phan.

# Using Phan

## Setting up Phan

**[[Getting Started]]**<br />
Start here for getting Phan installed and running on your code base.

**[[Annotating Your Source Code]]**<br />
This document describes how to add types to your code via doc block annotations. (e.g. `@param`, `@return`, `@var`, `@suppress`)

**[[About Union Types]]**<br />
An explanation of what union types are and how to use them.

**[[Tutorial for Analyzing a Large Sloppy Code Base]]**<br/>
A tutorial providing some guidance on how to get started analyzing your code and get to a place where static analysis is actually useful.

## Improving Phan Analysis

**[[Incrementally Strengthening Analysis]]**<br />
A guide to how Phan can be configured to slowly increase the strength of the analysis.

- [Adding Phan plugins to your project](https://github.com/phan/phan#features-provided-by-plugins) can strengthen analysis in other ways.

**[[Issue Types Caught by Phan]]**<br />
An enumeration of error types, examples that cause them and tips on how to resolve the issues

**[[Typing Parameters]]**<br />
A guide to handling interactions between Phan's `@param` types and declared parameter types or type-hints.

**[[How To Use Stubs]]**<br />
Details on setting up a `stubs` directory for making code available to Phan that isn't loaded on its runtime.

**[[Speeding up Phan Analysis]]**<br/>
A list of suggestions that may help speed up Phan analysis on your project.

## Using Advanced Features

**[[Generic Types]]**<br/>
Phan has primordial support for generic (templated) classes via type the annotations `@template` and `@inherits` and via a type syntax of the form `Class<T>` that may be referenced within doc-block annotations.

**[[Editor Support]]**<br/>
This article explains how to set up support for Phan in an editor/IDE.
Vim, Emacs, and VS Code have plugins/extensions written for them. This article also explains how to create plugins/extensions for an editor.

**[[Using Phan Daemon Mode]]**<br/>
Daemon mode lets you request Phan results from your editor or IDE to detect Phan issues for a single file, with much lower latency than a full analysis.

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

[![Build Status](https://dev.azure.com/tysonandre775/phan/_apis/build/status/phan.phan?branchName=v4)](https://dev.azure.com/tysonandre775/phan/_build/latest?definitionId=3&branchName=v4)
[![Build Status (Windows)](https://ci.appveyor.com/api/projects/status/github/phan/phan?branch=v4&svg=true)](https://ci.appveyor.com/project/TysonAndre/phan/branch/v4)
[![Gitter](https://badges.gitter.im/phan/phan.svg)](https://gitter.im/phan/phan?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

## Table of Contents

- **[Introduction](#introduction)**
- **[Submitting Patches](#submitting-patches)**
- **[Core Concepts](#core-concepts)** (Describes core concepts of phan)
  - **[FQSEN](#fqsen)** : A Fully Qualified Structural Element Name.
  - **[Type and UnionType](#type-and-uniontype)** (Representations of PHP data types, sets(union) of those types)
  - **[CodeBase](#code-base)** (maps FQSENs to a representation of the object for both scanned code and internal PHP elements)
  - **[Context](#context)** (represents the state of the world at any point during parsing or analysis)
  - **[Scope](#scope)** (maps variable names to `Variable`s and is housed within the `Context`)
  - **[Structural Elements](#structural-elements)** (Identified by FQSENs)
- **[Parsing and Analysis](#parsing-and-analysis)** (Describes the phases Phan takes to analyze code)
- **[Logging Issues](#logging-issues)** (How to log an `Issue` from Phan)
- **[AST Node Visitors](#ast-node-visitors)** (Used to implement many operations acting on multiple types of AST Nodes)
- **[Miscellaneous Advice](#miscellaneous-advice)** (Advice that may be useful when working on Phan)
- **[Testing your changes](#testing-your-changes)**

## Introduction
One of the big changes in PHP 7 is the fact that the parser now uses a real [Abstract Syntax Tree](https://wiki.php.net/rfc/abstract_syntax_tree).
This makes it much easier to write code
analysis tools by pulling the tree and walking it looking for interesting things.

Phan has 2 passes. On the first pass it reads every file, gets the AST and recursively parses it
looking only for functions, methods and classes in order to populate a bunch of
global hashes which will hold all of them. It also loads up definitions for all internal
functions and classes.
The type info for these come from a big file called [FunctionSignatureMap. (and its deltas)](https://github.com/phan/phan/tree/v4/src/Phan/Language/Internal).

The real complexity hits you hard in the second pass. Here some things are done recursively depth-first
and others not.
For example, we catch something like `foreach($arr as $k=>$v)` because we need to tell the
foreach code block that `$k` and `$v` exist. For other things we need to recurse as deeply as possible
into the tree before unrolling our way back out.
And for code such as `c(b(a(1)))` we need
to call `a(1)` and check that `a()` actually takes an int, then get the return type and pass it to `b()`
and check that, before doing the same to `c()`.

There is a Scope object which keeps track of all variables. It mimics PHP's scope handling in that it
has a globals along with entries for each function, method and closure. This is used to detect
undefined variables and also type-checked on a `return $var`.

## Submitting Patches

See [the Contribution Guidelines](https://github.com/phan/phan/blob/v4/.github/CONTRIBUTING.md) for some guidance on making great issues and pull requests.


## Core Concepts

There are a few concepts that are important to understand when looking at the Phan code.

### FQSEN

An [FQSEN](https://github.com/phan/phan/blob/v4/src/Phan/Language/FQSEN.php) is a Fully Qualified Structural Element Name.
Any element in PHP that can be accessed from elsewhere has an FQSEN that Phan uses to look it up.
For example, in the following code:

```php
namespace NS;

class A {
    const C = 'foo';
    public $p = 42;
    public function f() {}
}

function g() {}
```

we have the following fully qualified structural element names;

* **\NS\A** is the class `A` in namespace `\NS`
* **\NS\A::C** is the constant `C` in class `\NS\A`
* **\NS\A::$p** is the property `$p` in class `\NS\A`
* **\NS\A::f** is function `f` in the class `\NS\A`
* **\NS::g** is the function `g` in namespace `\NS`

It's important to note that even if we didn't have a namespace defined for the code, we'd still be in the implicit root namespace `\`. For example, in the code

```php
class B {
    function f() {}
}
function g() {}
```

we have the following FQSENs.

* **\B** is the class `B`
* **\B::f** is the function `f` in class `B`
* **\g** is the function `g`

Phan will use the **first occurrence** of an FQSEN (either the declaration or a usage) to render it elsewhere. Internally, it maps the normalized stringified FQSEN to an FQSEN instance with the original casing (e.g. converting namespace and class names and global function names to lowercase).  We'll likely make an option for enforcing casing in function and class names in future releases of Phan.

An FQSEN for an element will inherit from the abstract class [\Phan\Language\FQSEN](https://github.com/phan/phan/blob/v4/src/Phan/Language/FQSEN.php). The actual FQSEN for each element (classes, methods, constants, properties, functions) will be defined by classes in the [\Phan\Language\FQSEN](https://github.com/phan/phan/tree/v4/src/Phan/Language/FQSEN) namespace.

### Type and UnionType

A [Type](https://github.com/phan/phan/blob/v4/src/Phan/Language/Type.php) is what you'd expect and can be a native type like `int`, `float`, `string`, `bool`, `array` or a non-native type for a class such as `\Phan\Language\Type`. Types can also be a generic array such as `int[]`, `string[]`, `\Phan\Language\Type[]`, etc. which denote an array of type `int`, `string` and `\Phan\Language\Type` respectively.

A [UnionType](https://github.com/phan/phan/blob/v4/src/Phan/Language/UnionType.php) denotes a set of types for which an element can be any of them. A UnionType could be something like `int|string` to denote that something can be an `int` or a `string`.  See [[About Union Types]] for more details.

```php
/** @param bool|array $a */
function f($a) : int {
    return 42;
}
```

In the code above, the parameter `$a` is defined to be either a `bool` or an `array`. Passing the argument `true` or `[1, 2, 3]` would both pass analysis while passing `"string"` or `42` would not.


### Code Base

The [CodeBase](https://github.com/phan/phan/blob/v4/src/Phan/CodeBase.php) in Phan is an object that maps FQSENs to a representation of the object for both scanned code and internal PHP elements.

Classes, for instance are stored and looked up from the [Class Map](https://github.com/phan/phan/blob/v4/src/Phan/CodeBase/ClassMap.php) whereby a class can be fetched via the class `getClassByFQSEN`.

```php
$class = $code_base->getClassByFQSEN(
    FullyQualifiedClassName::fromFullyQualifiedString("\NS\A")
);
```

The CodeBase maps FQSENs of classes, methods, constants, properties and functions to their corresponding subclasses of Element.

### Context

The [Context](https://github.com/phan/phan/blob/v4/src/Phan/Language/Context.php) represents the state of the world at any point during parsing or analysis. It stores the following information:

* The file we're looking at (available via `getFile()`)
* The line number we're on (available via `getLine()`)
* The namespace we're in (available via `getNamespace()`)
* Any namespace maps that are available to us (available via `getNamespaceMapFor(...)`)
* The scope holding any variables available to us (available via `getScope()`)

and when appropriate, it also stores:

* The class we're in (available via `getClassFQSEN()`)
* The method we're in (available via `getMethodFQSEN()`)
* The function we're in (also available via `getMethodFQSEN()`)
* The closure we're in (available via `getClosureFQSEN()`)

The Context is used to map non-fully-qualified names to fully-qualified names (FQSENs) and for knowing which files and lines to associate errors with.

### Scope

The [Scope](https://github.com/phan/phan/blob/v4/src/Phan/Language/Scope.php) maps variable names to [variables](https://github.com/phan/phan/blob/v4/src/Phan/Language/Element/Variable.php) and is housed within the [Context](https://github.com/phan/phan/blob/v4/src/Phan/Language/Context.php). The Scope will be able to map both locally defined variables and globally available variables.

### Structural Elements

The universe of structural element types are defined in the [`\Phan\Language\Element`](https://github.com/phan/phan/tree/v4/src/Phan/Language/Element) namespace and limited to

* [**Clazz**](https://github.com/phan/phan/tree/v4/src/Phan/Language/Element/Clazz.php) is any class, interface or trait. It provides access to constants, properties or methods.
* [**Method**](https://github.com/phan/phan/tree/v4/src/Phan/Language/Element/Method.php) is a method, function or closure.
* [**Parameter**](https://github.com/phan/phan/tree/v4/src/Phan/Language/Element/Parameter.php) is a parameter to a function, method or closure.
* [**Variable**](https://github.com/phan/phan/tree/v4/src/Phan/Language/Element/Variable.php) is any global or local variable.
* [**Constant**](https://github.com/phan/phan/tree/v4/src/Phan/Language/Element/Constant.php) is either a global or class constant.
* [**Comment**](https://github.com/phan/phan/tree/v4/src/Phan/Language/Element/Comment.php) is a representation of a comment in code. These are stored only for classes, functions, methods, constants and properties.
* [**Property**](https://github.com/phan/phan/tree/v4/src/Phan/Language/Element/Property.php) is any property on a class, trait or interface.

## Parsing and Analysis

Phan runs in three phases;

1. **Parsing**
   All code is parsed in order to build maps from FQSENs to elements (such as classes or methods). During this phase the AST is read for each file and any addressable objects are created and stored in a map within the code base. A good place to start for understanding parsing is [\Phan\Parse\ParseVisitor](https://github.com/phan/phan/blob/v4/src/Phan/Parse/ParseVisitor.php).

2. **Class and Type Expansion**
   Before analysis can begin we take a pass over all elements (classes, methods, functions, etc.) and expand them with any information that was needed from the entire code base. Classes, for instance, get all constants, properties and methods from parents, interfaces and traits imported. The types of all classes are expanded to include the types of their parent classes, interfaces and traits.

3. **Analysis**
   Now that we know about all elements throughout the code base, we can start doing analysis.
   During analysis, we take another pass at reading the AST for all files so that we can start doing proofs on types and stuff.
   To understand analysis, take a look at [\Phan\Analysis\PreOrderAnalysisVisitor](https://github.com/phan/phan/blob/v4/src/Phan/Analysis/PreOrderAnalysisVisitor.php) and [\Phan\Analysis\PostOrderAnalysisVisitor](https://github.com/phan/phan/blob/v4/src/Phan/Analysis/PostOrderAnalysisVisitor.php).

A great place to start to understand how parsing and analysis happens is in [\Phan\Phan](https://github.com/phan/phan/blob/v4/src/Phan/Phan.php) where each step is explained.

Take a look at the [\Phan\Analysis](https://github.com/phan/phan/tree/v4/src/Phan/Analysis) namespace to see the various bits of analysis being done.

## Logging Issues

Issues found during analysis are emitted via the [`Issue::maybeEmit()`](https://github.com/phan/phan/blob/2.0.0/src/Phan/Issue.php#L4153-L4185) method. A common usage is below:

```php
Issue::maybeEmit(
    $this->code_base,  // The CodeBase
    $this->context,    // A Context with the file the issue is being emitted in
    Issue::TypeInvalidMethodName,  // The issue type
    $node->lineno,     // The line within the file the issue is being emitted in
    $method_name_type  // 0 or more arguments for the issue's format string
);
```

In this example, we're logging an issue of type `Issue::TypeInvalidMethodName` where we're passing something other than an string as the name of a dynamic call to an instance method, and we're noting that its in a given file on a given line, and including the type we saw (instead of `string`).
Each type of issue will take different parameters to fill into the message template.
`Issue::maybeEmit` is a variadic function that takes the CodeBase, the Context (with the file where the issue was seen), the issue type, the line number where it was seen and then anything that should be passed to `sprintf` to populate values in the issue template string.
In this case, `Issue::TypeInvalidMethodName` has [a template string](https://github.com/phan/phan/blob/2.0.0/src/Phan/Issue.php#L1814-L1821) that takes one `{TYPE}` (alias of `%s`) parameter, which is the type of the expression (passed in as `$method_string`.

Most of the time, you want `Issue::maybeEmit()` or `Issue::maybeEmitWithParameters()`, so that false positives can be suppressed in codebases using your plugin. Many classes have helper methods with names such as `emitIssue` (in classes defining $this->code_base and $this->context), to make emitting issues less verbose.

## AST Node Visitors

Many parts of Phan are implemented as AST Node Visitors whereby we switch on the type of node and call the appropriate method on the visitor. As an example, consider the following code.

```php
class A {
    public function f() {}
}
```

During the parsing phase, we'd

* Create an AST in [\Phan\Phan::parseFile](https://github.com/phan/phan/blob/567e427af2f82434b086780fce63c3b8ba48035f/src/Phan/Phan.php#L140-L146)
* Create a [ParseVisitor](https://github.com/phan/phan/blob/v4/src/Phan/Analysis/ParseVisitor.php) for the root node in [\Phan\Phan::parseNodeInContext](https://github.com/phan/phan/blob/567e427af2f82434b086780fce63c3b8ba48035f/src/Phan/Phan.php#L200-L206)
* Visit the class node via [ParseVisitor::visitClass](https://github.com/phan/phan/blob/567e427af2f82434b086780fce63c3b8ba48035f/src/Phan/Analysis/ParseVisitor.php#L77)
* Visit the method node via [ParseVisitor::visitMethod](https://github.com/phan/phan/blob/567e427af2f82434b086780fce63c3b8ba48035f/src/Phan/Analysis/ParseVisitor.php#L239)

Other node visitors include

* [\Phan\Analysis\PreOrderAnalysisVisitor](https://github.com/phan/phan/blob/v4/src/Phan/Analysis/PreOrderAnalysisVisitor.php) where we do part of the analysis during the analysis phase.
* [\Phan\Analysis\PostOrderAnalysisVisitor](https://github.com/phan/phan/blob/v4/src/Phan/Analysis/PostOrderAnalysisVisitor.php) where we do another part of the analysis during the analysis phase.
* [\Phan\AST\UnionTypeVisitor](https://github.com/phan/phan/blob/v4/src/Phan/AST/UnionTypeVisitor.php) where we do much of the work figuring out the types of things throughout analysis.

### Miscellaneous Advice

`\Phan\Debug` is a collection of utilities that may be useful to you when working on Phan patches or plugins.
For example, `\Phan\Debug::printNode(\ast\Node $node)` will print a compact representation of an AST node (and it's kind and flags) to stdout.

- [`Element::VISIT_LOOKUP_TABLE`](https://github.com/phan/phan/blob/v4/src/Phan/AST/Visitor/Element.php) tells you what visitor methods are called for a given `\ast\Node->kind`.
- `php internal/dump_fallback_ast.php --php-ast '2 xor 3;'` can be used if you want to quickly see what AST kind and flags a given expression (or php file's contents) would have.

When adding new functionality, it often helps to check if there is any existing functionality or issues that is similar to what you want to implement, and search for references (a good place to start looking is where the corresponding issue types are emitted. e.g. to find out where `PhanTypeInvalidMethodName` is emitted, search the codebase for `TypeInvalidMethodName`).

A [`\Phan\AST\ContextNode`](https://github.com/phan/phan/blob/v4/src/Phan/AST/ContextNode.php) contains a lot of useful functionality, such as locating the definition(s) of an element from a referencing node (class, function-like, property, etc) in a Context.

### Testing your changes

See [tests/README.md](https://github.com/phan/phan/tree/v4/tests#scripts) for how to run tests and what Phan's various tests do.

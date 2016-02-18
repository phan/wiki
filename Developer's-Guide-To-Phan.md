[![Build Status](https://travis-ci.org/etsy/phan.svg?branch=master)](https://travis-ci.org/etsy/phan) [![Gitter](https://badges.gitter.im/etsy/phan.svg)](https://gitter.im/etsy/phan?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

This is a guide for developers looking to hack on Phan.

## Submitting Patches

See [the Contribution Guidelines](https://github.com/etsy/phan/blob/master/.github/CONTRIBUTING.md) for some guidance on making great issues and pull requests.


## Core Concepts

There are a few concepts that are important to understand when looking at the Phan code.

### FQSEN

An [FQSEN](https://github.com/etsy/phan/blob/master/src/Phan/Language/FQSEN.php) is a Fully Qualified Structural Element Name. Any element in PHP that can be accessed from elsewhere has an FQSEN that we use to look it up. In the following code for example

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

* **\NS\a** is the class `A` in namespace `\NS`
* **\NS\a::C** is the constant `C` in class `\NS\a`
* **\NS\a::$p** is the property `$p` in class `\NS\a`
* **\NS\a::f** is function `f` in the class `\NS\a`
* **\NS::g** is the function `g` in namespace `\NS`

Its important to note that even if we didn't have a namespace defined for the code, we'd still be in the implicit root namespace `\`. For example, in the code

```php
class B {
    function f() {}
}
function g() {}
```

we have the following FQSENs.

* **\b** is the class `B`
* **\b::f** is the function `f` in class `B`
* **\g** is the function `g`

You'll note that all class and function FQSENs lowercase the name. We do this because function and class names are case insensitive in PHP. We'll likely make an option for enforcing casing in function and class names in future releases of Phan.

An FQSEN for an element will inherit from the abstract class [\Phan\Language\FQSEN](https://github.com/etsy/phan/blob/master/src/Phan/Language/FQSEN.php). The actual FQSEN for each element (classes, methods, constants, properties, functions) will be defined by classes in the [\Phan\Language\FQSEN](https://github.com/etsy/phan/tree/master/src/Phan/Language/FQSEN) namespace.

## Type and UnionType

A [Type](https://github.com/etsy/phan/blob/master/src/Phan/Language/Type.php) is what you'd expect and can be a native type like `int`, `float`, `string`, `bool`, `array` or a non-native type for a class such as `\Phan\Language\Type`. A type can also be a generic array such as `int[]`, `string[]`, `\Phan\Language\Type[]`, etc. which denote an array of type `int`, `string` and `\Phan\Language\Type` respectively.

A [UnionType](https://github.com/etsy/phan/blob/master/src/Phan/Language/UnionType.php) denotes a set of types for which an element can be any of them. A UnionType could be something like `int|string` to denote that something can be an `int` or a `string`.

```php
/** @param bool|array $a */
function f($a) : int {
    return 42;
}
```

In the code above, the parameter `$a` is defined to be either a `bool` or an `array`. Passing the argument `true` or `[1, 2, 3]` would both pass analysis while passing `"string"` or `42` would not.


## Code Base

The [CodeBase](https://github.com/etsy/phan/blob/master/src/Phan/CodeBase.php) in Phan is an object that maps FQSENs to a representation of the object for both scanned code and internal PHP elements.

Classes, for instance are stored and looked up from the [Class Map](https://github.com/etsy/phan/blob/master/src/Phan/CodeBase/ClassMap.php) whereby a class can be fetched via the class `getClassByFQSEN`.

```php
$class = $code_base->getClassByFQSEN(
    FullyQualifiedClassName::fromFullyQualifiedString("\NS\a")
);
```

The CodeBase maps classes, methods, constants, properties and functions.

## Context

The [Context](https://github.com/etsy/phan/blob/master/src/Phan/Language/Context.php) represents the state of the world at any point during parsing or analysis. It stores

* The file we're looking at (available via `getFile()`)
* The line number we're on (available via `getLine()`)
* The namespace we're in (available via `getNamespace()`)
* Any namespace maps that are available to us (available via `getNamespaceMapFor(...)`)
* The scope holding any variables available to us (available via `getScope()`)

and when appropriate

* The class we're in (available via `getClassFQSEN()`)
* The method we're in (available via `getMethodFQSEN()`)
* The function we're in (also available via `getMethodFQSEN()`)
* The closure we're in (available via `getClosureFQSEN()`)

The Context is used to map non-fully-qualified names to fully-qualified names (FQSENs) and for knowing which files and lines to associate errors with.

## Scope

The [Scope](https://github.com/etsy/phan/blob/master/src/Phan/Language/Scope.php) maps variable names to [variables](https://github.com/etsy/phan/blob/master/src/Phan/Language/Element/Variable.php) and is housed within the [Context](https://github.com/etsy/phan/blob/master/src/Phan/Language/Context.php). The Scope will be able to map both locally defined variables and globally available variables.

## Structural Elements

The universe of structural element types are defined in the [`\Phan\Language\Element`](https://github.com/etsy/phan/tree/master/src/Phan/Language/Element) namespace and limited to

* [**Clazz**](https://github.com/etsy/phan/tree/master/src/Phan/Language/Element/Clazz.php) is any class, interface or trait. It provides access to constants, properties or methods.
* [**Method**](https://github.com/etsy/phan/tree/master/src/Phan/Language/Element/Method.php) is a method, function or closure.
* [**Parameter**](https://github.com/etsy/phan/tree/master/src/Phan/Language/Element/Parameter.php) is a parameter to a function, method or closure.
* [**Variable**](https://github.com/etsy/phan/tree/master/src/Phan/Language/Element/Variable.php) is any global or local variable.
* [**Constant**](https://github.com/etsy/phan/tree/master/src/Phan/Language/Element/Constant.php) is either a global or class constant.
* [**Comment**](https://github.com/etsy/phan/tree/master/src/Phan/Language/Element/Comment.php) is a representation of a comment in code. These are stored only for classes, functions, methods, constants and properties.
* [**Property**](https://github.com/etsy/phan/tree/master/src/Phan/Language/Element/Property.php) is any property on a class, trait or interface.

## Parsing and Analysis

Phan runs in three phases;

1. **Parsing**
   All code is parsed in order to build maps from FQSENs to elements (such as classes or methods). During this phase the AST is read for each file and any addressable objects are created and stored in a map within the code base. A good place to start for understanding parsing is [\Phan\Analyze\ParseVisitor](https://github.com/etsy/phan/blob/master/src/Phan/Analyze/ParseVisitor.php).

2. **Class and Type Expansion**
   Before analysis can begin we take a pass over all elements (classes, methods, functions, etc.) and expand them with any information that was needed from the entire code base. Classes, for instance, get all constants, properties and methods from parents, interfaces and traits imported. The types of all classes are expanded to include the types of their parent classes, interfaces and traits.

3. **Analysis**
   Now that we know about all elements throughout the code base, we can start doing analysis. During analysis we take another pass at reading the AST for all files so that we can start doing proofs on types and stuff. To understand analysis, take a look at [\Phan\Analyze\PreOrderAnalysisVisitor](https://github.com/etsy/phan/blob/master/src/Phan/Analyze/PreOrderAnalysisVisitor.php) and [\Phan\Analyze\PostOrderAnalysisVisitor](https://github.com/etsy/phan/blob/master/src/Phan/Analyze/PostOrderAnalysisVisitor.php).

A great place to start to understand how parsing and analysis happens is in [\Phan\Phan](https://github.com/etsy/phan/blob/master/src/Phan/Phan.php) where each step is explained.

Take a look at the [\Phan\Analyze](https://github.com/etsy/phan/tree/master/src/Phan/Analyze) namespace to see the various bits of analysis being done.

## Logging Issues

Issues found during analysis are emitted via the [Issue::emit](https://github.com/etsy/phan/blob/79b02a398751af156ff1c512867ee7006654e175/src/Phan/Issue.php#L482-L485) method. A common usage is

```php
Issue::emit(
    Issue::TypeMismatchForeach,
    $this->context->getFile(),
    $node->lineno ?? 0,
    (string)$expression_type
);
```

In this example, we're logging an issue of type `Issue::TypeMismatchForeach` where we're passing something other than an array as the first argument to a `foreach` and we're noting that its in a given file on a given line. Each type of issue will take different parameters to fill into the message template. `Issue::emit` is a variadic function that takes the type of issue, the file where the issue was seen, the line number where it was seen and then anything that should be passed to `sprintf` to populate values in the issue template string. In this case, `Issue::TypeMismatchForeach` has [a template string](https://github.com/etsy/phan/blob/79b02a398751af156ff1c512867ee7006654e175/src/Phan/Issue.php#L261) that takes one `%s` parameter, which is the type of the expression (passed in as `(string)$expression_type`.

## AST Node Visitors

Many parts of Phan are implemented as AST Node Visitors whereby we switch on the type of node and call the appropriate method on the visitor. As an example, consider the following code.

```php
class A {
    public function f() {}
}
```

During the parsing phase, we'd

* Create an AST in [\Phan\Phan::parseFile](https://github.com/etsy/phan/blob/567e427af2f82434b086780fce63c3b8ba48035f/src/Phan/Phan.php#L140-L146)
* Create a [ParseVisitor](https://github.com/etsy/phan/blob/master/src/Phan/Analyze/ParseVisitor.php) for the root node in [\Phan\Phan::parseNodeInContext](https://github.com/etsy/phan/blob/567e427af2f82434b086780fce63c3b8ba48035f/src/Phan/Phan.php#L200-L206)
* Visit the class node via [ParseVisitor::visitClass](https://github.com/etsy/phan/blob/567e427af2f82434b086780fce63c3b8ba48035f/src/Phan/Analyze/ParseVisitor.php#L77)
* Visit the method node via [ParseVisitor::visitMethod](https://github.com/etsy/phan/blob/567e427af2f82434b086780fce63c3b8ba48035f/src/Phan/Analyze/ParseVisitor.php#L239)

Other node visitors include

* [\Phan\Analyze\PreOrderAnalysisVisitor](https://github.com/etsy/phan/blob/master/src/Phan/Analyze/PreOrderAnalysisVisitor.php) where we do part of the analysis during the analysis phase.
* [\Phan\Analyze\PostOrderAnalysisVisitor](https://github.com/etsy/phan/blob/master/src/Phan/Analyze/PostOrderAnalysisVisitor.php) where we do another part of the analysis during the analysis phase.
* [\Phan\AST\UnionTypeVisitor](https://github.com/etsy/phan/blob/master/src/Phan/AST/UnionTypeVisitor.php) where we do much of the work figuring out the types of things throughout analysis.
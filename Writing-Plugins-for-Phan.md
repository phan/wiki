Phan comes with a mechanism for adding plugins for your code base. Plugins have hooks for analyzing every node in the AST for every file, classes, methods and functions.

Plugin code lives in your repo and is referenced from your phan config file, but runs in the Phan execution environment with access to all Phan APIs.

# Creating a Plugin

To create a plugin, you'll need to

* Create a plugin file for which the last line returns an instance of a class extending [`\Phan\Plugin`](https://github.com/etsy/phan/blob/master/src/Phan/Plugin.php)
* Add a reference to the file in `.phan/config.php` under the `plugin` array.

Phan contains an example plugin named [DemoPlugin](https://github.com/etsy/phan/blob/master/.phan/plugins/DemoPlugin.php) that is referenced from [Phan's .phan/config.php file](https://github.com/etsy/phan/blob/92552016b2d3c650f5c625a8f64a9db935a756d6/.phan/config.php#L117).

A more meaningful real-world example is given in [DollarDollarPlugin](https://github.com/etsy/phan/blob/master/.phan/plugins/DollarDollarPlugin.php) which checks to make sure there are no variable of the form `$$var` in Phan's code base.

# How Plugins Work

A plugin file returns an instance of a class extending [\Phan\Plugin](https://github.com/etsy/phan/blob/master/src/Phan/Plugin.php) and has four hooks.

* `analyzeNode`
* `analyzeClass`
* `analyzeMethod`
* `analyzeFunction`

As the method names suggest, they analyze AST nodes, classes, methods and functions respectively.

When issues are found, they can be emitted to the log via a call to `emitIssue`.

```php
$this->emitIssue(
    $code_base,
    $context,
    'PhanPluginMyPluginType',
    "Message associated with the issue."
);
```

where `$code_base` is the `CodeBase` object passed to your hook, `$context` is the context in which the issue is found (such as the `$context` passed to the hook, or from `$class->getContext()`, `$method->getContext()` or `$function->getContext()`, a name for the issue type (allowing it to be suppressed via @suppress) and the message to emit to the user.



# Reference Material

When writing plugins, you'll likely need to understand a few concepts. The following contains some material that may be useful. You can learn more from the [Developer's Guide to Phan](https://github.com/etsy/phan/wiki/Developer%27s-Guide-To-Phan)

## Node
A `Node` is an AST node returned from the [php-ast](https://github.com/nikic/php-ast) PHP extension. You can [read more about its interface in its README](https://github.com/nikic/php-ast#api-overview). You'll also find many references to `Node` that can be copied throughout the Phan code base.

## Clazz
The [Clazz](https://github.com/etsy/phan/blob/master/src/Phan/Language/Element/Clazz.php) class contains things you'll need to know about a class such as its FQSEN (fully-qualified structural element name), name, type, context, and flags such as `isAbstract`, `isInterface`, `isTrait`, etc.

## Method
The [Method](https://github.com/etsy/phan/blob/master/src/Phan/Language/Element/Method.php) class contains things you'll need to know about methods such as its FQSEN, name, parameters, return type, etc..

## Func
Similarly, the [Func](https://github.com/etsy/phan/blob/master/src/Phan/Language/Element/Func.php) class contains things you'll need to know about functions.

## Context
A [Context](https://github.com/etsy/phan/blob/master/src/Phan/Language/Context.php) is a thing defined for every line of code that tells you which file you're in, which line you're on, which class you're in, which methods, function or closure you're in and the [Scope](https://github.com/etsy/phan/blob/master/src/Phan/Language/Scope.php) that is available to you which contains all local and global variables.

## UnionType
A [UnionType](https://github.com/etsy/phan/blob/master/src/Phan/Language/UnionType.php) is a set of [Type](https://github.com/etsy/phan/blob/master/src/Phan/Language/Type.php)s defined for an object such as `int|string|DateTime|null`. [You can read more about UnionTypes here](https://github.com/etsy/phan/wiki/About-Union-Types).

You'll likely find yourself getting types frequently via a call to [`UnionType::fromNode(...)`](https://github.com/etsy/phan/blob/16b54d01217e19965eb293b455d5df1ccacb2c46/src/Phan/Language/UnionType.php#L124-L157) such as with

```php
$union_type = UnionType::fromNode($context, $code_base, $node);
```

providing you the union type of the statement defined by the AST node `$node`.

## CodeBase
A [CodeBase](https://github.com/etsy/phan/blob/master/src/codebase.php) is a thing containing a mapping from all FQSENs (fully qualified structural element names) to their associated objects (Classes, Methods, Functions, Properties, Constants).

You can use the codebase to look up info on any objects you find references to. More typically, you'll need to keep the `$code_base` around to pass it to all of Phan's methods.
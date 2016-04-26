Phan comes with a mechanism for adding plugins for your code base. Plugins have hooks for analyzing every node in the AST for every file, classes, methods and functions.


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
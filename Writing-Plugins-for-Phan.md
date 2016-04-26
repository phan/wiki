Phan comes with a mechanism for adding plugins for your code base. Plugins have hooks for analyzing every node in the AST for every file, classes, methods and functions.


# Creating a Plugin

To create a plugin, you'll need to

* Create a plugin file for which the last line returns an instance of a class extending `\Phan\Plugin`
* Add a reference to the file in `.phan/config.php` under the `plugin` array.

Phan contains an example plugin named [DemoPlugin](https://github.com/etsy/phan/blob/master/.phan/plugins/DemoPlugin.php) that is referenced from [Phan's .phan/config.php file](https://github.com/etsy/phan/blob/92552016b2d3c650f5c625a8f64a9db935a756d6/.phan/config.php#L117)


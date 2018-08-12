The original plugin types will work in all known versions of Phan,
and must be used for Phan <= 0.8.4, but won't be as efficient as V2 plugins.

Support for v1 plugins was removed in Phan 1.0.0.

## Creating a Plugin (Legacy)

To create a plugin, you'll need to

* Create a plugin file for which the last line returns an instance of a class extending [`\Phan\Plugin`](https://github.com/phan/phan/blob/master/src/Phan/Plugin.php)
* Add a reference to the file in `.phan/config.php` under the `plugin` array.

Phan contains an example plugin named [DemoLegacyPlugin](https://github.com/phan/phan/blob/master/.phan/plugins/DemoLegacyPlugin.php) that is referenced from [Phan's .phan/config.php file](https://github.com/phan/phan/blob/92552016b2d3c650f5c625a8f64a9db935a756d6/.phan/config.php#L117).

A more meaningful real-world example is given in [DollarDollarPlugin](https://github.com/phan/phan/blob/0.12.13/.phan/plugins/DollarDollarPlugin.php) which checks to make sure there are no variable of the form `$$var` in Phan's code base.

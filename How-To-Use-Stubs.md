From time to time you may encounter situations where there are entities that are used throughout a code base that Phan doesn't have access to. This may happen if your production environment uses a PHP extension that Phan doesn't have access to.

# Stubs

Creating stubs that Phan has access to is pretty straight forward.

1. Create a directory `.phan/stubs` ([like Phan's](https://github.com/etsy/phan/tree/master/.phan/stubs)).
2. Put code in there that stubs the constants/classes/properties/methods/functions of interest.
3. Reference the `.phan/stubs` directory from within `.phan/config.php` under `directory_list` ([like Phan's](https://github.com/etsy/phan/blob/0655d1ed47e776ab281b91fd3ad0a9835e03b75a/.phan/config.php#L221)).

JetBrains makes a very large number of stubs available at [github.com/JetBrains/phpstorm-stubs](https://github.com/JetBrains/phpstorm-stubs/tree/master/standard). You may wish to consider using some of these for classes you need access to.
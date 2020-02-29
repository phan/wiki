To get Phan running on a Mac with Homebrew, ensure that Homebrew is installed (see [http://brew.sh](http://brew.sh)) and then run the following:

Note: as of [Homebrew 1.5.0, the homebrew/php TAP was removed](https://brew.sh/2018/01/19/homebrew-1.5.0/) (and replaced with [`php` in core](https://formulae.brew.sh/formula/php)), so the installation instructions are more complicated

If you had the homebrew/php tap installed, or are upgrading from Homebrew < 1.5.0, then first run the following commands:

```sh
brew update; # updates local brew metadata
# removes old homebrew php TAP, now php is in homebrew core
brew untap homebrew/php;
brew list | grep 'php'; # check for all php related installs
# php72-ast never existed, I think, so leave out brew uninstall php72-ast
brew uninstall php; # or brew uninstall php72 if you had that before
```

---

Then, to install PHP 7.4 and php-ast, run the following commands ([let me know if they do or don't work](https://github.com/phan/phan/issues/1637)):

**Note: If you are already using homebrew-php for other applications, upgrading homebrew (and removing the homebrew/php TAP) may cause other issues.** For example, you may need to install other missing extensions and applications)

```sh
brew upgrade; # make sure all the other libs are up to date
brew cleanup; # cleanup old files
brew prune; # cleanup old symlinks
brew install autoconf; # Required by pecl
brew install php; # or php@7.2, or php@7.3, or php@7.4
pecl channel-update pecl.php.net;
# the below command should not echo ast. If it does, then it is already installed,
# and you don't need to run pecl install ast
php --modules | grep 'ast';
# Install other pecl modules that you are still missing in a similar fashion
# This may become out of date, see Phan's README for the recommended php-ast version.
pecl install ast-1.0.6;
php --modules | grep 'ast'; # should echo ast
# Note: If the project being analyzed had other dependencies such as APCu,
# you may wish to install those with pecl or look at
# https://github.com/phan/phan/wiki/How-To-Use-Stubs#internal-stubs
# If you were using php fpm and such prior to upgrading Phan
# (Completely unrelated to phan)
### brew services start php;
```

Once that completes successfully, you can check that phan is working correctly via any of the two other installation methods: [(1) from phan.phar](https://github.com/phan/phan/wiki/Getting-Started/#from-phanphar) or [(2) from source](https://github.com/phan/phan/wiki/Getting-Started/#from-source)

```sh
# or phan.phar --help
phan --help
```

You can create an alias or symlink to phan for convenience (e.g. add an alias in `.bashrc`), or mark phan.phar as executable and move it into a folder in your `$PATH` environment variable):

```sh
# alias phan=/path/to/phan.phar
# alias phan=/path/to/phan-git-checkout/phan
```

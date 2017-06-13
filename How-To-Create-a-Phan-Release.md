We create releases for Phan whenever we want to get new bug fixes or features out to folks that choose to use stable releases.

As of this writing, we're supporting two active versions; 0.8.x for PHP 7.0 syntax and 0.9.x for PHP 7.1 syntax.

# Release Checklist

When creating a new release, make sure you do the following.

- [ ] Update version number under `CLI::PHAN_VERSION` in [\Phan\CLI](https://github.com/etsy/phan/blob/master/src/Phan/CLI.php#L16).
- [ ] Build Phar by running `php package.php` and getting the output under `build/phan.phar`.
- [ ] [Create a new release](https://github.com/etsy/phan/releases), and uploading the `phan.phar` file.
  - [ ] Make sure you mark it as "pre-release"
  - [ ] In the description, note the difference between the 0.8 branch and 0.9.
  - [ ] Copy or link the release notes from [NEWS](https://github.com/etsy/phan/blob/master/NEWS) into the release description.


After creating the new release, check [packagist.org/packages/etsy/phan](https://packagist.org/packages/etsy/phan) to make sure it picked up the new release.

Additional post-release tasks:

- [ ] Notify the maintainers of homebrew-php that a new Phan release exists and/or create a PR for [0.8.x](https://github.com/Homebrew/homebrew-php/pull/4219) and [0.9.x](https://github.com/Homebrew/homebrew-php/pull/4220)
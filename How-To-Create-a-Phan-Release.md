We create releases for Phan whenever we want to get new bug fixes or features out to folks that choose to use stable releases.

As of this writing, we're supporting three active versions; 0.8.x for PHP 7.0 syntax and 0.9.x for PHP 7.1 syntax.

- Support for 0.9 will be dropped after a few patch releases of 0.10.0 (possibly around December)
- After that, 0.8.x will start using AST version 50, to minimize the difference with 0.10.0

# Release Checklist

When creating a new release, make sure you do the following.

- [ ] Update version number under `CLI::PHAN_VERSION` in [\Phan\CLI](https://github.com/phan/phan/blob/master/src/Phan/CLI.php#L16).
- [ ] Build Phar by running `php package.php` and getting the output under `build/phan.phar`. See https://github.com/phan/phan/issues/880
- [ ] [Create a new release](https://github.com/phan/phan/releases), and uploading the `phan.phar` file.
  - [ ] Make sure you mark it as "pre-release"
  - [ ] In the description, note the difference between the 0.8 branch and 0.9 and 0.10.
  - [ ] Copy or link the release notes from [NEWS](https://github.com/phan/phan/blob/master/NEWS) into the release description.


After creating the new release, check [packagist.org/packages/phan/phan](https://packagist.org/packages/phan/phan) to make sure it picked up the new release.

Additional post-release tasks:

- [ ] Create PRs to change Phan version in NEWS and CLI.php to 0.8.(x+1)-dev and 0.9.(y+1)-dev and 0.10.(y+1)-dev
- [ ] Notify the maintainers of [homebrew-php](https://github.com/Homebrew/homebrew-php) that a new Phan release exists and/or create a PR for [0.8.x](https://github.com/Homebrew/homebrew-php/pull/4219) and [0.9.x](https://github.com/Homebrew/homebrew-php/pull/4220) and 0.10.x
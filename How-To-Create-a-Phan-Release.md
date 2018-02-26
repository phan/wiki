We create releases for Phan whenever we want to get new bug fixes or features out to folks that choose to use stable releases.

As of this writing, we're supporting one active version: 0.12.x for PHP 7.0, 7.1, and 7.2 syntax

- Support for versions predating 0.12.0 has been dropped

# Release Checklist

When creating a new release, make sure you do the following.

- [ ] Update version number and date of [NEWS.md](https://github.com/phan/phan/blob/master/NEWS.md).
- [ ] Update version number under `CLI::PHAN_VERSION` in [\Phan\CLI](https://github.com/phan/phan/blob/master/src/Phan/CLI.php#L16).
- [ ] Build Phar by running `internal/make_phar` and getting the output under `build/phan.phar`. See https://github.com/phan/phan/issues/880
- [ ] [Create a new release](https://github.com/phan/phan/releases), and uploading the `phan.phar` file.
  - [ ] Make sure you mark it as "pre-release"
  - [ ] Copy or link the release notes from [NEWS.md](https://github.com/phan/phan/blob/master/NEWS.md) into the release description.


After creating the new release, check [packagist.org/packages/phan/phan](https://packagist.org/packages/phan/phan) to make sure it picked up the new release.

Additional post-release tasks:

- [ ] Create PRs to change Phan version in NEWS.md and CLI.php to 0.12.(x+1)-dev
- [ ] Notify the maintainers of [homebrew-php](https://github.com/Homebrew/homebrew-php) that a new Phan release exists and/or create a PR for [0.12.x](https://github.com/Homebrew/homebrew-php/pull/4219)
We create releases for Phan whenever we want to get new bug fixes or features out to folks that choose to use stable releases.

As of this writing, we're supporting one active version: 4.x.y for the analysis of PHP 7.0 – 8.0 syntax.

- Support for versions predating 4.0.0 has been dropped

# Release Checklist

When creating a new release, make sure you do the following.

- [ ] Update version number and date of [NEWS.md](https://github.com/phan/phan/blob/v4/NEWS.md).
- [ ] Update version number under `CLI::PHAN_VERSION` in [\Phan\CLI](https://github.com/phan/phan/blob/v4/src/Phan/CLI.php#L59).
- [ ] Build Phar by running `internal/make_phar` and getting the output under `build/phan.phar`. See https://github.com/phan/phan/issues/880
- [ ] Generate a phar signature with `gpg -u identity@domain --detach-sign --output build/phan.phar.asc build/phan.phar` (See [#1759](https://github.com/phan/phan/issues/1759)). If this is a new key, mention the id and signature there.
- [ ] [Create a new release](https://github.com/phan/phan/releases), and uploading the `phan.phar` file.
  - [ ] Make sure you **do not** mark it as "pre-release", unless we decide to release a release candidate or beta or alpha.
  - [ ] Copy or link the release notes from [NEWS.md](https://github.com/phan/phan/blob/v4/NEWS.md) into the release description.

After creating the new release, check [packagist.org/packages/phan/phan](https://packagist.org/packages/phan/phan) to make sure it picked up the new release.

Additional post-release tasks:

- [ ] Create PRs to change Phan version in NEWS.md and CLI.php to 4.x.(y+1)-dev
- [ ] Update the most recent Phan version in the wiki (optional for patch releases)

We create releases for Phan whenever we want to get new bug fixes or features out to folks that choose to use stable releases.

As of this writing, we're supporting one active version: 5.x.y for the analysis of PHP 7.0 – 8.1 syntax.

- Support for versions predating 5.0.0 has been dropped

# Release Checklist

When creating a new release, make sure you do the following.

- [ ] Update version number and date of [NEWS.md](https://github.com/phan/phan/blob/v5/NEWS.md) ([example](https://github.com/phan/phan/commit/100677e9a898b55ccf73cf40f2273479ed1dc8e0)).
- [ ] Update version number under `CLI::PHAN_VERSION` in [\Phan\CLI](https://github.com/phan/phan/blob/3a17b9697c63b4807c6cddaa4112962e49cce5d0/src/Phan/CLI.php#L87) ([example](https://github.com/phan/phan/commit/100677e9a898b55ccf73cf40f2273479ed1dc8e0#diff-c006dc0d81b8e98eb6a8596c2b5a099490ebd9370a9a5607b609ecde3e96a610)).
- [ ] Build Phar by running `internal/make_phar` and getting the output under `build/phan.phar`. See https://github.com/phan/phan/issues/880
- [ ] Generate a phar signature with `gpg -u identity@domain --detach-sign --output build/phan.phar.asc build/phan.phar` (See [#1759](https://github.com/phan/phan/issues/1759)). If this is a new key, mention the id and signature there ([example](https://github.com/phan/phan/issues/1759#issuecomment-2251391648)).
- [ ] [Create a new release](https://github.com/phan/phan/releases)
  - [ ] Set the Tag to be the new version number you are releasing.
  - [ ] Upload the `phan.phar` and `phan.phar.asc` files.
  - [ ] Make sure you **do not** mark it as "pre-release", unless we decide to release a release candidate or beta or alpha.
  - [ ] Copy or link the release notes from [NEWS.md](https://github.com/phan/phan/blob/v5/NEWS.md) into the release description.

After creating the new release, check [packagist.org/packages/phan/phan](https://packagist.org/packages/phan/phan) to make sure it picked up the new release.

Additional post-release tasks:

- [ ] Create PRs to change Phan version in NEWS.md and CLI.php to 5.x.(y+1)-dev. [Example](https://github.com/phan/phan/commit/790e4f76301bc084c1024d3febd211c5ce01460c).
- [ ] Update the most recent Phan version in the wiki (optional for patch releases)

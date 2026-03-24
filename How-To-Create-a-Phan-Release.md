We create releases for Phan whenever we want to get new bug fixes or features out to folks that choose to use stable releases.

As of this writing, we're supporting one active version: 6.x.y on the `v6` branch for the analysis of PHP 8.1 – 8.5 syntax.

- The `v5` branch is maintained for bug fixes only
- Support for versions predating 5.0.0 has been dropped

# Release Checklist

When creating a new release, make sure you do the following.

- [ ] Update version number and date of [NEWS.md](https://github.com/phan/phan/blob/v6/NEWS.md) ([example](https://github.com/phan/phan/commit/100677e9a898b55ccf73cf40f2273479ed1dc8e0)).
- [ ] Update version number under `CLI::PHAN_VERSION` in [\Phan\CLI](https://github.com/phan/phan/blob/v6/src/Phan/CLI.php#L87) ([example](https://github.com/phan/phan/commit/100677e9a898b55ccf73cf40f2273479ed1dc8e0#diff-c006dc0d81b8e98eb6a8596c2b5a099490ebd9370a9a5607b609ecde3e96a610)).
- [ ] Build Phar by running `internal/make_phar` and getting the output under `build/phan.phar`. See https://github.com/phan/phan/issues/880
- [ ] Generate a phar signature with `gpg -u identity@domain --detach-sign --output build/phan.phar.asc build/phan.phar` (See [#1759](https://github.com/phan/phan/issues/1759)). If this is a new key, mention the id and signature there ([example](https://github.com/phan/phan/issues/1759#issuecomment-2251391648)).
- [ ] [Create a new release](https://github.com/phan/phan/releases)
  - [ ] Set the Tag to be the new version number you are releasing.
  - [ ] Upload the `phan.phar` and `phan.phar.asc` files.
  - [ ] Make sure you **do not** mark it as "pre-release", unless we decide to release a release candidate or beta or alpha.
  - [ ] Copy or link the release notes from [NEWS.md](https://github.com/phan/phan/blob/v6/NEWS.md) into the release description.

After creating the new release, check [packagist.org/packages/phan/phan](https://packagist.org/packages/phan/phan) to make sure it picked up the new release.

# Post-Release Tasks

- [ ] Update `CLI::PHAN_VERSION` in `src/Phan/CLI.php` to `6.x.(y+1)-dev` ([example](https://github.com/phan/phan/commit/790e4f76301bc084c1024d3febd211c5ce01460c)).
- [ ] Add a new `6.x.(y+1)-dev` header to `NEWS.md`.
- [ ] Update the most recent Phan version in the wiki (optional for patch releases).

# Updating the Wiki

When a release includes new features, update the relevant wiki pages so the documentation stays in sync. Common updates:

- **New config options** — Run the config doc auto-generator and copy the result to the wiki checkout:
  ```bash
  ./vendor/bin/phpunit tests/Phan/Internal/WikiConfigTest.php
  # If the test fails, the internal copy is out of date:
  cp internal/Phan-Config-Settings.md.new internal/Phan-Config-Settings.md
  cp internal/Phan-Config-Settings.md /path/to/phan.wiki/Phan-Config-Settings.md
  ```
  New settings also need a category entry in `tests/Phan/Internal/ConfigEntry.php` — without one they land in "misc" or are hidden. See [[Phan Config Settings|Phan-Config-Settings]].

- **New CLI flags** — Update [[Using Phan From Command Line|Using-Phan-From-Command-Line]].

- **New built-in plugins** — Update [[Built-in Plugins|Built-in-Plugins]].

- **New plugin capabilities** — Update [[Writing Plugins for Phan|Writing-Plugins-for-Phan]].

- **New issue types or analysis features** — Update [[Issue Types Reference|Issue-Types-Reference]] or the relevant annotation/type system page.

- **New PHP version support** — Update [[PHP 8.4/8.5 Support|PHP-8.4-8.5-Support]] (or create a new page for the version).

Push wiki changes directly to the `master` branch of the [wiki repo](https://github.com/phan/phan.wiki):
```bash
# Clone the wiki repo if you haven't already:
# git clone https://github.com/phan/phan.wiki.git /path/to/phan.wiki
cd /path/to/phan.wiki
git add -A && git commit -m "Update docs for 6.x.y release"
git push origin master
```

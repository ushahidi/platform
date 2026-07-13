# symm/gisconverter (vendored)

This is a vendored copy of [`symm/gisconverter`](https://packagist.org/packages/symm/gisconverter)
v1.0.5, at upstream commit `21ab697b7692b891dac37c64d10c9d83d4433f80`.

## Why is this here?

**The upstream repository no longer exists.** `github.com/symm/gisconverter` returns 404, and
the package is marked abandoned on Packagist. Packagist still serves the metadata, but the only
`dist` URL it offers points at the deleted GitHub repo, so `composer install` fails with:

```
Failed to download symm/gisconverter from dist: The "https://api.github.com/repos/symm/
gisconverter/zipball/21ab697b7692b891dac37c64d10c9d83d4433f80" file could not be downloaded
(HTTP/2 404)
```

That made the project unbuildable from a clean checkout. There is no surviving fork to point
at, so the source is vendored here and wired up with a Composer `path` repository in the root
`composer.json`. Nothing that imports `Symm\Gisconverter\...` had to change.

The library is load-bearing: it does the WKT geometry decoding used by `PointRepository`,
`BoundingBox`, `PostPoint`, the V5 geometry query handlers and the Twitter data source.

## Contents

`src/` is byte-identical to the v1.0.5 release. The upstream `composer.json` was trimmed to
what we need (dropping `require-dev`, `minimum-stability` and the `dev-master` branch alias)
and pinned with an explicit `version`, which a `path` repository requires.

Upstream's tests, `example.php` and its (long dead) Travis config were not copied.

## Licence

Unchanged: modified BSD (3-clause), Copyright (c) 2010-2011 Arnaud Renevier. See `LICENSE.txt`.
BSD-3-Clause is compatible with this project's AGPL-3.0, and the copyright notice and
disclaimer are retained as that licence requires.

## If you are replacing this

Should a maintained fork appear, drop the `path` repository entry from the root `composer.json`,
delete this directory, and point `symm/gisconverter` at the new source.

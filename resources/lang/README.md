# Language files for Ushahidi Platform API

## How can I contribute?

**Translations** are managed in Transifex, not here. Please don't send pull requests
against the translated locale directories -- the translations are pulled from Transifex
with `tx pull` (see `.tx/config`), and a future pull will overwrite anything edited
directly in this repo.

To contribute a translation, register on transifex.com and ask to join our translation
project here:

https://www.transifex.com/ushahidi/ushahidi-v3-api

**Source (English) strings** are the exception: they live in `resources/lang/en/` and are
edited in this repo by pull request, the same as any other code change. Adding a new
string means adding it there first, so that it can then be translated in Transifex.

Note that the Transifex pull is run manually -- there is no CI job that syncs it.

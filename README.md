[download]: https://wiki.ushahidi.com/display/WIKI/Ushahidi+v3.x+Downloads
[install]: https://wiki.ushahidi.com/display/WIKI/Installing+Ushahidi+3.x
[wiki]: https://wiki.ushahidi.com/display/WIKI/Ushahidi+Platform+v3.x
[getin]: https://wiki.ushahidi.com/display/WIKI/Ushahidi+v3.x+-+Getting+Involved
[ushphab]: https://phabricator.ushahidi.com/
[helpphab]: https://phabricator.ushahidi.com/w/help/phabricator/
[ush2]: https://github.com/ushahidi/Ushahidi_Web
[ushahidi]: http://ushahidi.com
[ushblog]: http://blog.ushahidi.com

Ushahidi 3
============

[![Build Status](https://travis-ci.org/ushahidi/platform.png)](https://travis-ci.org/ushahidi/platform)

[Download][download]

[Installation Guide][install]

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy)

## What is Ushahidi 3.x?

Ushahidi is an open source web application for information collection, visualization,
and interactive mapping. It helps collect data from many sources, including: email,
SMS text messaging, Twitter streams, and RSS feeds. The platform offers tools to
help process that information, categorize it, geo-locate it and publish it on a map.

Ushahidi 3.x is the latest iteration of this tool, rebuilt from the ground up.
Our codebase has been completely rethought, and so has the way we think about
users interacting with mobile and social data.
Crowdsourcing strategies have come a long way since Ushahidi began and we've
been fortunate to learn so much from our dedicated global community.

### Should I use Ushahidi 3.x for my new project?

We don't recommend it right now as the project is under active development.

The platform is nearing completion as we focus on building the new frontend and fixing outstanding bugs.

### I'm a developer, should I contribute to Ushahidi 3.x?

We would love your help! Understand the platform is in active development
which means that parts of the codebase may change rapidly. If you're keen
to help build something awesome and happy to get deep into the core workings,
then please read about [getting involved][getin]!

Most of our active development tasks are organized on the [Ushahidi Phabricator][ushphab].
If you haven't used Phabricator before, read [Phab Help][helpphab] after you sign up.

## Using the Platform

Please see our [Installation Guide][install] to get set up first!

### Logging in for the first time

The default install creates the user `demo` with a password of `testing`.
This user has admin privileges. Once logged in, this user can create more user
accounts or give admin permissions to others as well.

### Configuration

Base config files are located in `application/config/`.
You can add per-environment config overrides in `application/config/environments/`.
The environment is determined based on the `KOHANA_ENV` environment variable.

Routes are configured in `application/routes/default.php`. Additional routes can
be added in per-environment routing files ie. `application/routes/development.php`.

Extras
------

### Vagrantfile

We've included a Vagrantfile and puppet manifests to help build a quick development box.
Install [Vagrant](http://www.vagrantup.com/), then run `vagrant up` to get started!

### Travis-CI

Unit and functional tests are run automatically by [Travis-CI](https://travis-ci.org/ushahidi/platform).
See [.travis.yml](https://github.com/ushahidi/platform/blob/master/.travis.yml) for config details.

### Testing

See the [3.x Testing](https://wiki.ushahidi.com/display/WIKI/3.x+Testing) page.

We use PHPUnit for unit tests, and Behat and Mink for functional testing.
You can install the Behat, Mink, PHPUnit and other required packages using
[Composer](getcomposer.org). Just run:

```bash
composer install
```

Behat and PHPUnit will be installed to `bin/` at the root of the repository.
Run the tests with:

```bash
bin/behat --config application/tests/behat.yml --format progress
bin/phpunit -c application/tests/phpunit.xml
```

#### Creating feature tests

Create your feature file in `application/tests/features/`, eg `foo.bar.feature`.
You can run a single test with:

```bash
bin/behat --config application/tests/behat.yml application/tests/features/foo.bar.feature --format progress
```

#### Creating unit tests

Create your test file in `application/tests/classes/Acme/`, eg `FooBarTest.php`.
You can run a single test with:

```bash
bin/phpunit -c application/tests/phpunit.xml application/tests/classes/Acme/FooBarTest.php
```

## Useful Links

- [Download][download]
- [Installation Guide][install]
- [Wiki][wiki]
- [Get Involved][getin]
- [Phabricator][ushphab]
  - [Phabricator Help][helpphab]
- [Ushahidi][ushahidi]
  - [Ushahidi Blog][ushblog]
- [Ushahidi Platform v2][ush2]

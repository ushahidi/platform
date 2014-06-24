Ushahidi 3
============

[![Build Status](https://travis-ci.org/ushahidi/platform.png)](https://travis-ci.org/ushahidi/platform)

[Download](https://wiki.ushahidi.com/display/WIKI/Ushahidi+3.x+Downloads)

## What is Ushahidi 3.x?

Ushahidi is an open source web application for information collection, visualization,
and interactive mapping. It helps collect data from many sources, including: email,
SMS text messaging, Twitter streams, and RSS feeds. The platform offers tools to
help process that information, categorize it, geo-locate it and publish it on a map.

Ushahidi 3.x is the next iteration of this tool, rebuilt from the ground up.
Not only the code but the way in which we think about users interacting with mobile
and social data. Crowdsourcing strategies have come a long way in the five years
Ushahidi has been around and we've been fortunate enough to learn a lot from our
global community.

### Should I use Ushahidi 3.x for my new project?

We don't recommend it right now. The platform is not complete and there lots of bugs.

### I'm a developer, should I contribute to Ushahidi 3.x?

We would love your help, but the platform is in heavy development with a rapid rate
of change. If you're keen to help build something awesome, and happy to get deep
into the core workings... then yes! Read about [getting involved][getin] page.
Most of our active development happens on the [Ushahidi Phabricator][ushphab].
If you haven't used Phabricator before, read [Phab Help][helpphab] after you sign up.

If you just want to fix a few bugs, or build a prototype on Ushahidi, you're probably
better helping out on [Ushahidi 2.x][ush2] right now.

[getin]: https://wiki.ushahidi.com/display/WIKI/Ushahidi+v3.x+-+Getting+Involved
[ushphab]: https://phabricator.ushahidi.com/
[helpphab]: https://phabricator.ushahidi.com/w/help/phabricator/
[ush2]: https://github.com/ushahidi/Ushahidi_Web

## More info

- [Download](https://wiki.ushahidi.com/display/WIKI/Ushahidi+3.x+Downloads)
- [The Wiki](https://wiki.ushahidi.com/display/WIKI/Ushahidi,+v3.X)
- [Ushahidi (the organisation)](http://ushahidi.com)
- [Ushahidi Blog](http://blog.ushahidi.com)

### Logging in the first time

The default install creates a user `demo` with password `testing`. This user has
admin privileges. Once logged in this user can create further user accounts or
give others admin permissions too.

### Configuration

Base config files are in `application/config/`. You can add per-environment config
overrides in `application/config/environments/`. The environment is switched based
on the `KOHANA_ENV` environment variable.

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

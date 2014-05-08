Ushahidi 3
============

[![Build Status](https://travis-ci.org/ushahidi/Lamu.png)](https://travis-ci.org/ushahidi/Lamu)

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

[getin]: https://wiki.ushahidi.com/display/WIKI/Ushahidi%2C+v3.x+-+Getting+Involved)
[ushphab]: https://phabricator.ushahidi.com/
[helpphab]: https://phabricator.ushahidi.com/w/help/phabricator/
[ush2]: https://github.com/ushahidi/Ushahidi_Web

## More info

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

Release Notes
-------------

### What to expect in the latest Alpha (aka v3.0.0-alpha.3)

There's a bunch of new things in this release, you can search and edit posts, edit site settings, manage users, pull messages from SMS and turn them in to posts. One of the simplest but most major improvements: you can log in, register and log out! And you can access the public site without logging in at all!
As with the previous release you should still be able to get V3 installed, create and delete posts, view a list of posts and drill down to individual post pages

#### What's not working:
* Uploading images on posts
* Showing custom form field on posts
* User profile in the workspace menu still display dummy content
* Posts still display dummy images
* Permission checks in the UI - we check permissions thoroughly throughout the API however this isn't always reflected in the UI. This means you'll sometimes see a UI for editing something (ie. users) but be unable to actually load an data or unable to edit the data.
* Related posts - always shows the most recent 3 posts
* Media - We're just using fake images at the moment, there's no way to upload new ones
* Custom forms - these exist in the API, but there's no UI for managing them.

#### How do I get admin access?

The default install creates a user 'demo' with password 'testing'. This user has admin privileges. Once logged in this user can create further user accounts or give others admin permissions too.

#### Authorization (aka. why does it keep asking me to 'Authorize This Request'?)

When logging in you still get a standard OAuth authorization screen. This is because our UI is using the API directly, and the standard authorization flows. We've improved this a lot since last release and we're working on getting rid of the authorize screen completely for the default UI client.

#### How do I pull in SMS or Email

This is working but the config is still in code. The main config is covered in `application/config/data-providers.php`. We'll be publishing a detailed guide on how to do this soon!

Extras
------

### Vagrantfile

We've included a Vagrantfile and puppet manifests to help build a quick development box.
Install [Vagrant](http://www.vagrantup.com/), then run `vagrant up` to get started!

### Travis-CI

Unit and functional tests are run automatically by [Travis-CI](https://travis-ci.org/ushahidi/Lamu).
See [.travis.yml](https://github.com/ushahidi/Lamu/blob/master/.travis.yml) for config details.

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

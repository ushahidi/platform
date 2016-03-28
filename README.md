[download]: https://github.com/ushahidi/platform/releases
[install]: https://www.ushahidi.com/support/install-ushahidi
[docs]: https://www.ushahidi.com/support
[getin]: https://www.ushahidi.com/support/get-involved
[issues]: https://github.com/ushahidi/platform/issues
[ush2]: https://github.com/ushahidi/Ushahidi_Web
[ushahidi]: http://ushahidi.com
[ushblog]: http://blog.ushahidi.com

Ushahidi 3
============

[![Build Status](https://travis-ci.org/ushahidi/platform.png)](https://travis-ci.org/ushahidi/platform)
[![Stories up next](https://badge.waffle.io/ushahidi/platform.png?label=2 - Up Next&title=Up Next)](https://waffle.io/ushahidi/platform)
[![Coverage Status](https://coveralls.io/repos/github/ushahidi/platform/badge.svg)](https://coveralls.io/github/ushahidi/platform)

[Download][download]

[Installation Guide][install]

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy)

## What is Ushahidi?

Ushahidi is an open source web application for information collection, visualization and interactive mapping. It helps you to collect info from: SMS, Twitter, RSS feeds, Email. It helps you to process that information, categorize it, geo-locate it and publish it on a map.

## What is Ushahidi v3?

Ushahidi v3 is the next iteration of this tool, rebuilt from the ground up -- not only the code but the way in which we think about users interacting with mobile and social data.

Crowdsourcing strategies have come a long way in the five years Ushahidi has been around and we've been fortunate enough to learn a lot from our global community.

### Should I use Ushahidi v3 for my new project?

That depends.. we've released v3 to the public and it should be usable in production.
However it's still missing a few features we had in v2. We recommend you give it a try and see if it meets your needs. If somethings missing let us know! It helps us prioritise future development

### I'm a developer, should I contribute to Ushahidi v3?

Yes! Development moves pretty quickly but the tech stack is getting more and more stable. If you're keen to help build something awesome, [Jump on board..][getin]

## Using the Platform

Please see our [Installation Guide][install] to get set up first!

### Logging in for the first time

The default install creates the user `admin` with a password of `admin`.
This user has admin privileges. Once logged in, this user can create more user
accounts or give admin permissions to others as well.

Extras
------

### Vagrantfile

We've included a Vagrantfile and puppet manifests to help build a quick development box.
Install [Vagrant](http://www.vagrantup.com/), then run `vagrant up` to get started!

### Travis-CI

Unit and functional tests are run automatically by [Travis-CI](https://travis-ci.org/ushahidi/platform).
See [.travis.yml](https://github.com/ushahidi/platform/blob/master/.travis.yml) for config details.

## Useful Links

- [Download][download]
- [Installation Guide][install]
- [Documentation][docs]
- [Get Involved][getin]
- [Bug tracker][issues]
- [Ushahidi][ushahidi]
  - [Ushahidi Blog][ushblog]
- [Ushahidi Platform v2][ush2]

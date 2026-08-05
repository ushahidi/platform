[client]: https://github.com/ushahidi/platform-client
[download]: https://github.com/ushahidi/platform-release/releases
[setup-guides]: https://docs.ushahidi.com/platform-developer-documentation/development-and-code/setup_alternatives
[support]: https://www.ushahidi.com/support
[rest-api-docs]: https://docs.ushahidi.com/platform-developer-documentation/tech-stack/api-documentation
[getin]: https://www.ushahidi.com/support/get-involved
[issues]: https://github.com/ushahidi/platform/issues
[ush2]: https://github.com/ushahidi/Ushahidi_Web
[ushahidi]: http://ushahidi.com

Ushahidi Platform
=================

## What is Ushahidi Platform?

Ushahidi Platform is an open source web application for information collection, visualization and interactive mapping. It helps you to collect info from: SMS, Twitter, RSS feeds, Email. It helps you to process that information, categorize it, geo-locate it and publish it on a map.

This repository contains the backend code with the REST API implementation.

Head over to the [Platform Client repository][client] for the browser app code.

## Setup essentials

The shortest path to get up and running is:

- Install Docker Engine
- Install Make command (parses Makefile)
- Run `make start`

The backend will be listening on localhost:8080.

> **What about the browser client application?**

> Once your Platform backend is running, head over to the [platform-client-mzima](https://github.com/ushahidi/platform-client-mzima) repository to get the in-browser Platform experience!

### Other helpful commands

You may use `make start` to restart the containers (does a full container build).

You may use `make apply` to apply dependency and migration changes to containers (without full container build). **Note:** this requires containers to be up.
​
To stop Docker containers run `make stop`

To take everything down (including deleting the database) `make down` will do that for you.



**WIP**: to run the automated tests ...

## Manuals and documentation

### A note for grassroots organizations
If you are starting a deployment for a grassroots organization, you can apply for a free social-impact responder account [here](https://www.ushahidi.com/pricing/apply-for-free) after verifying that you meet the criteria.


### Platform User Manual

The official reference on how to use the Platform. Create surveys, configure data sources... it's all in there!
[Platform User Manual](https://docs.ushahidi.com/platform-user-manual/)

### Platform Developer Documentation

Key pointers on installing and developing on the Platform.

[Platform Developer Documentation](https://docs.ushahidi.com/platform-developer-documentation/)

## Credits

## Contributors ✨

Thanks goes to the wonderful people who [[Contribute](CONTRIBUTING.md)]! See the list of contributors at [all-contributors](docs/contributors-to-ushahidi.md)
This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!

## Useful Links
- [Code of Conduct](https://docs.ushahidi.com/platform-developer-documentation/code-of-conduct)
- [Download][download]
- [Installation guides](https://docs.ushahidi.com/platform-developer-documentation/v/set-up-the-developer-environment-for-ushahidi/)
- [Developer and User Support][support]
- [REST API docs][rest-api-docs]
- [Get Involved][getin]
- [Bug tracker][issues]
- [About Ushahidi][ushahidi]
- [Ushahidi Platform v2][ush2]

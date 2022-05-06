# Migration from AngularJS

**Introduction**

The Platform Client is built upon Angular JS 1.5.6, which is currently in [Long Term Support ](https://docs.angularjs.org/misc/version-support-status)ending in December 2021. Because of this, Ushahidi wants to move away from this version of Angular and into a more modern JavaScript framework and architecture. In March 2021, we started the journey which will be carried out incrementally until the last piece of AngularJS code is removed.

In order to do the migration, we will make use of Micro Frontend architecture and "single-spa", a framework for bringing together multiple JavaScript micro-frontends in a frontend application.

The goals of this project are:

* Make the Ushahidi Platform more stable with fewer bugs
* Make the Ushahidi Platform faster on slow networks
* Stop depending on technologies that have been abandoned
* Migrate away from AngularJS
* Improve performance of the Platform Client
* Modernise handling of styles

**The plan**

_The first release, planned for October 2021_

* Migrated Page settings and meta-tags __ and general setup of single-spa
* Restructured codebase, all views are divided into separate modules in the legacy app, which sets the foundation of the migration of the UI
* Lazy loading separate views in the legacy-app
* Updated documentation

Future releases:

* Migrate parts of the UI
  * Modebar ← Release 2
  * Activity-view ←Release 3
  * Settings-view ← Release 4
  * Data and Map view ← Release 5

Decisions to be made:

* Decide on a UI framework to Migrate TO
* Decide on the handling of styles

**What does this mean for contributors to the Platform Client?**

You can keep contributing as before but be aware that the structure of the code has changed and will change more in the upcoming months. There will be a lot of moving parts. Also be prepared to start working in a new framework eventually, once that decision has been made.




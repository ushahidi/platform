# Development: Overview

## Overview

### What does Ushahidi Do? <a id="what-does-ushahidi-do"></a>

* Ushahidi is a tool for collecting, managing, and visualizing data.
* Data can be collected from anyone, anytime, anywhere by SMS, email, web, Twitter, and RSS.
* Posts can be managed and triaged with filters and workflows.
* Data can be viewed in many ways: on a map, in a list, or as a visualization.

### Who is Ushahidi For? <a id="who-is-ushahidi-for"></a>

Anyone can use Ushahidi, but traditionally it has been a tool used by Crisis Responders, Human Rights Reporters, and Citizens & Governments \(such as election monitoring or corruption reporters\). We also serve environmental mappers, asset monitoring, citizen journalism, international development, and many others.

### Technical Specifications <a id="technical-specifications"></a>

**Development stack**

{% hint style="warning" %}
**TODO: Add info about v4.**
{% endhint %}

* Ushahidi 3.x was built on a PHP stack: dependencies are managed with composer, we’re using Kohana 3 but phasing that out, and we’ve isolated the core logic of the platform standalone Entity and Usecase classes.
* The user interface of Ushahidi 3.x is now a separate app \(the client\) built purely in JS, HTML + CSS using AngularJS and a collection of other libraries, with a build pipeline using gulp and Webpack.
* What’s new \(and improved\)?
  * Dependencies are properly managed and easier to update or replace needed.
  * We’re using our own API to build the app, it gets first class support. 
  * You can work on just the UI without delving into the API code
  * Modern libraries mean they’re still being supported, we don’t have the burden of supporting legacy libraries ourselves.

#### Code is easier to customize

* code is more structured making it easier to find what you want
* code is doesn’t repeat itself so a change can be made in one place, not need to be copied everywhere else
* UI is isolated to the client, allowing work on just the UI without having to delve into the API code

#### The stack

Back-end: [Linux](http://en.wikipedia.org/wiki/Linux), [PHP](https://php.net/), [Apache](http://httpd.apache.org/)/[Nginx](http://wiki.nginx.org/Main), [MySQL](http://www.mysql.com/) or [PostgreSQL](http://www.postgresql.org/)

Front-end: [AngularJS](https://angularjs.org/), [Javascript](http://en.wikipedia.org/wiki/JavaScript), [Html](http://en.wikipedia.org/wiki/HTML), [CSS](http://en.wikipedia.org/wiki/Cascading_Style_Sheets). Built with [NodeJS](http://nodejs.org/), [Gulp.js](https://gulpjs.com/) and [Webpack](https://webpack.js.org/). Using [Leaflet](http://leafletjs.com/) for mapping, and a collection of other frontend libraries


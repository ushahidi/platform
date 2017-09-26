# Architecture

## High Level Data Flows

At a high level Ushahidi consumes messages from various channels (SMS, Email, Twitter, our own web interface), transforms these into posts. Ushahidi core stores and exposes this data in a REST API. The primary consumers of the REST API are our web client and mobile app.

![data-flow](./data-flow.png "Data Flow")
[source](https://www.planttext.com/?text=RP71Ri8m38RlVWehf-sGDq0LQ6CI1n2YJ3jKFGIQKaiXGOsd7YRU7RTgkmKj1ylvVyVvd2mZcvQ_hmw0YPt5pzYOXYh2TyC6Frpe08fZHyosBQ78jxd4zTMGAm5yg2ogwOZ27q1PBw-u3v6dN1tM-H5N-ur24x7VI3wRky1Kqzam1H_L80-Xc47UGcjBk0l6Dfn845Utcp1ysHDkl53LvYp-BwHkwTAmpWQ64JNL-Y4I1VeuASytmuYyqCxM__d5M50kvXPFS7ygidIAj9UkGkTrbhm9mDBwIdxe0G00)

## Application tiers

The Platform is split into 3 layers: Presentation (Client / Web interface), Services (API), and Data.

![app-tiers](./app-tiers.png "Application tiers")
[source](http://www.nomnoml.com/#view/%23title%3A%20Application%20Tiers%0A%0A%5BPresentation%7C%0A%20%20%20%20%5BAngularJS%5D%0A%20%20%20%20%5BEndpoints%5D%0A%5D%0A%0A%5BServices%7C%0A%20%20%20%20%5BAPI%5Do-%3E%5BKohana%5D%20%0A%20%20%20%20%5BAPI%5Do-%3E%5BUshahidi%20Core%5D%0A%20%20%20%20%5BKohana%5D--%3E%5BPHP%5D%0A%20%20%20%20%5BUshahidi%20Core%5D--%3E%5BPHP%5D%20%20%20%0A%5D%0A%0A%5BData%7C%0A%20%20%20%20%5BMySQL%5D%0A%5D%0A%0A%5BPresentation%5D%3C-%3E%5BServices%5D%0A%5BServices%5D%3C-%3E%5BData%5D%0A%0A%0A%23direction%3A%20right)

### API

The REST API provides all data access. This provides for the main Ushahidi user interface, but also any external services and partners that need to access data.

The API layer consists of a core application (models, usecases, etc) and a delivery layer (routing and controllers). The core application is pure object-oriented PHP and the delivery mechanism is a PHP application built using the Kohana Framework.

In theory the Kohana application could handle frontend views and interactions too, but splitting the API out allows us far greater flexibility in the variety of applications that we can build. Mobile apps, 3rd party integrations, etc are not 2nd class citizens: they can consume the same API as our primary frontend. The API has to be able to do everything to our data.

Containing the core business logic within a core application that is separate from the Kohana delivery layer allows us to test the core application, independent of the database (or any other implementation details) while also enforcing the internal API by which the rest of the system operates. This allows us to modify internal details, such as the database structure, without breaking the external API, as well as ensuring that the entire system remains in a stable, tested state.

### Web Client

The Frontend is a javascript application, built on AngularJS. It loads all data from the Platform API. The JS app is entirely static so can be hosted on any simple webserver.

### Data Layer (Mysql)

The database layer is a standard MySQL server. You can see a schema here [svg](schema.svg) [png](schema.png)

## Internal API Architecture

### API Delivery

Within the API there are two layers: the delivery and the business logic (core application). The delivery layer follows a Model View Controller (MVC) pattern, with the View consisting of JSON output. The Controllers use a [Service Locator](https://en.wikipedia.org/wiki/Service_locator_pattern) to load and execute various tools, taking the API request inputs and returning the requested resources.

#### Core Application

Within the core application, we use generally follow the [Clean Architecture](http://blog.8thlight.com/uncle-bob/2012/08/13/the-clean-architecture.html). The central part of the business logic is defined as use cases and entities. All dependencies flow inwards towards the entities, which have no dependencies.

![architecture-layers](./arch-layers.png "Software architecture layers")

In order to bring user input to the use cases, we pass simple data structures from the delivery layer into the use case. The request structure is a simple array and contains all of the inputs for that specific use case. Once the usecase is complete it returns another simple data structure (response) back to the delivery layer for conversion via a Formatter. Data flow within the platform can be visualized as:

![api-request-flow](./api-request-flow.png "API Request Flow")
[source](http://www.nomnoml.com/#view/%23title%3A%20General%20API%20request%20flow%0A%0A%5B%3Cstart%3Eapp%5D-%3E%5BKohana%5D%0A%5BKohana%5D-%3E%5BController%5D%0A%5BController%5D-%3E%5B%3Cstate%3Erequest%5D%0A%5B%3Cstate%3Erequest%5D-%3E%5BUsecase%5D%0A%5BUsecase%5D-%3E%5B%3Cstate%3Eresponse%5D%0A%5B%3Cstate%3Eresponse%5D-%3E%5BOutputFormatter%5D%0A%5BOutputFormatter%5D-%3E%5B%3Cend%3Ejson%5D%0A%0A%5B%3Cstate%3Erequest%7C%0Apayload%3B%0Aidentifier%3B%0Afilters%5D%0A%0A%5BDependencies%7C%0A%20Repository%3B%0A%20Validator%3B%0A%20Authorizer%3B%0A%20etc...%0A%5Do-%3E%5BUsecase%5D%0A%0A%23direction%3A%20right)

See [Use Case Internals](./use-case-internals.md) for more detail

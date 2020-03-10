# 📐 Architecture

## High Level Data Flows

At a high level Ushahidi consumes messages from various channels \(SMS, Email, Twitter, our own web interface\), transforms these into posts. Ushahidi core stores and exposes this data in a REST API. The primary consumers of the REST API are our web client and mobile app.

![Data Flow](../.gitbook/assets/data-flow-1%20%282%29.png) [source](https://www.planttext.com/?text=RP71Ri8m38RlVWehf-sGDq0LQ6CI1n2YJ3jKFGIQKaiXGOsd7YRU7RTgkmKj1ylvVyVvd2mZcvQ_hmw0YPt5pzYOXYh2TyC6Frpe08fZHyosBQ78jxd4zTMGAm5yg2ogwOZ27q1PBw-u3v6dN1tM-H5N-ur24x7VI3wRky1Kqzam1H_L80-Xc47UGcjBk0l6Dfn845Utcp1ysHDkl53LvYp-BwHkwTAmpWQ64JNL-Y4I1VeuASytmuYyqCxM__d5M50kvXPFS7ygidIAj9UkGkTrbhm9mDBwIdxe0G00)

## Application tiers

The Platform is split into 3 layers: Presentation \(Client / Web interface\), Services \(API\), and Data.

![Application tiers](../.gitbook/assets/app-tiers-1.png) \[source\]\([http://www.nomnoml.com/\#view/%23title%3A Application Tiers](http://www.nomnoml.com/#view/%23title%3A%20Application%20Tiers)

\[Presentation\| %20%20%20%20\[AngularJS\] %20%20%20%20\[Endpoints\] \]

\[Services\| %20%20%20%20\[API\]o-&gt;\[Kohana\]%20 %20%20%20%20\[API\]o-&gt;\[Ushahidi%20Core\] %20%20%20%20\[Kohana\]--&gt;\[PHP\] %20%20%20%20\[Ushahidi%20Core\]--&gt;\[PHP\]%20%20%20 \]

\[Data\| %20%20%20%20\[MySQL\] \]

\[Presentation\]&lt;-&gt;\[Services\] \[Services\]&lt;-&gt;\[Data\]

%23direction%3A%20right\)

### API

The REST API provides all data access. This provides for the main Ushahidi user interface, but also any external services and partners that need to access data.

The API layer consists of a core application \(models, usecases, etc\) and a delivery layer \(routing and controllers\). The core application is pure object-oriented PHP and the delivery mechanism is a PHP application built using the Kohana Framework.

In theory the Kohana application could handle frontend views and interactions too, but splitting the API out allows us far greater flexibility in the variety of applications that we can build. Mobile apps, 3rd party integrations, etc are not 2nd class citizens: they can consume the same API as our primary frontend. The API has to be able to do everything to our data.

Containing the core business logic within a core application that is separate from the Kohana delivery layer allows us to test the core application, independent of the database \(or any other implementation details\) while also enforcing the internal API by which the rest of the system operates. This allows us to modify internal details, such as the database structure, without breaking the external API, as well as ensuring that the entire system remains in a stable, tested state.

### Web Client

The Frontend is a javascript application, built on AngularJS. It loads all data from the Platform API. The JS app is entirely static so can be hosted on any simple webserver.

### Data Layer \(Mysql\)

The database layer is a standard MySQL server. You can see a schema here [svg](https://github.com/tuxpiper/platform/tree/fcc78a1dd925ff383509ac9e862ad295850d187f/docs/schema.svg) [png](https://github.com/tuxpiper/platform/tree/fcc78a1dd925ff383509ac9e862ad295850d187f/docs/schema.png)

## Internal API Architecture

### API Delivery

Within the API there are two layers: the delivery and the business logic \(core application\). The delivery layer follows a Model View Controller \(MVC\) pattern, with the View consisting of JSON output. The Controllers use a [Service Locator](https://en.wikipedia.org/wiki/Service_locator_pattern) to load and execute various tools, taking the API request inputs and returning the requested resources.

#### Core Application

Within the core application, we use generally follow the [Clean Architecture](http://blog.8thlight.com/uncle-bob/2012/08/13/the-clean-architecture.html). The central part of the business logic is defined as use cases and entities. All dependencies flow inwards towards the entities, which have no dependencies.

![Software architecture layers](../.gitbook/assets/arch-layers-1%20%281%29.png)

In order to bring user input to the use cases, we pass simple data structures from the delivery layer into the use case. The request structure is a simple array and contains all of the inputs for that specific use case. Once the usecase is complete it returns another simple data structure \(response\) back to the delivery layer for conversion via a Formatter. Data flow within the platform can be visualized as:

![API Request Flow](../.gitbook/assets/api-request-flow-1.png) \[source\]\([http://www.nomnoml.com/\#view/%23title%3A General API request flow](http://www.nomnoml.com/#view/%23title%3A%20General%20API%20request%20flow)

\[app\]-&gt;\[Kohana\] \[Kohana\]-&gt;\[Controller\] \[Controller\]-&gt;\[request\] \[request\]-&gt;\[Usecase\] \[Usecase\]-&gt;\[response\] \[response\]-&gt;\[OutputFormatter\] \[OutputFormatter\]-&gt;\[json\]

\[request\| payload%3B identifier%3B filters\]

\[Dependencies\| %20Repository%3B %20Validator%3B %20Authorizer%3B %20etc... \]o-&gt;\[Usecase\]

%23direction%3A%20right\)

See [Use Case Internals](use-case-internals.md) for more detail


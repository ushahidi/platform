# Requests

Kohana includes a flexible HMVC request system. It supports out of the box support for internal requests and external requests.

HMVC stands for `Hierarchical Model View Controller` and basically means requests can each have MVC triads called from inside each other.

The Request object in Kohana is HTTP/1.1 compliant.

## Creating Requests

Creating a request is very easy:

### Internal Requests

An internal request is a request calling to the internal application. It utilizes [routes](routing) to direct the application based on the URI that is passed to it. A basic internal request might look something like:

	$request = Request::factory('welcome');

In this example, the URI is 'welcome'.

#### The initial request

Since Kohana uses HMVC, you can call many requests inside each other. The first request (usually called from `index.php`) is called the "initial request". You can access this request via:

	Request::initial();

You should only use this method if you are absolutely sure you want the initial request. Otherwise you should use the `Request::current()` method.

#### Sub-requests

You can call a request at any time in your application by using the `Request::factory()` syntax. All of these requests will be considered sub-requests.

Other than this difference, they are exactly the same. You can detect if the request is a sub-request in your controller with the is_initial() method:

	$sub_request = ! $this->request->is_initial()

### External Requests

An external request calls out to a third party website.

You can use this to scrape HTML from a remote site, or make a REST call to a third party API:

	// This uses GET
	$request = Request::factory('http://www.google.com/');

	// This uses PUT
	$request = Request::factory('http://example.com/put_api')->method(Request::PUT)->body(json_encode('the body'))->headers('Content-Type', 'application/json');

	// This uses POST
	$request = Request::factory('http://example.com/post_api')->method(Request::POST)->post(array('foo' => 'bar', 'bar' => 'baz'));

## Executing Requests

To execute a request, use the `execute()` method on it. This will give you a [response](responses) object.

	$request = Request::factory('welcome');
	$response = $request->execute();

## Request Cache Control

You can cache requests for fast execution by passing a cache instance in as the second parameter of factory:

	$request = Request::factory('welcome', Cache::instance());

TODO

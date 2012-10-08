# Migrating from 3.1.x

## Config

The configuration system has been rewritten to make it more flexible. The most significant change is that config groups are now merged across all readers, similar to how files cascade within the cascading filesystem. Therefore you can now override a single config value (perhaps a database connection string, or a 'send-from' email address) and store it in a separate config source (environment config file / database / custom) while inheriting the remaining settings from that group from your application's configuration files.

In other respects, the majority of the public API should still operate in the same way, however the one major change is the transition from using `Kohana::config()` to `Kohana::$config->load()`, where `Kohana::$config` is an instance of `Config`.

`Config::load()` works almost identically to `Kohana::config()`, e.g.:

	Kohana::$config->load('dot.notation')
	Kohana::$config->load('dot')->notation

A simple find/replace for `Kohana::config`/`Kohana::$config->load` within your project should fix this.

The terminology for config sources has also changed.  Pre 3.2 config was loaded from "Config Readers" which both
read and wrote config.  In 3.2 there are **Config Readers** and **Config Writers**, both of which are a type of 
**Config Source**.

A **Config Reader** is implemented by implementing the `Kohana_Config_Reader` interface; similarly a **Config Writer**
is implemented by implementing the `Kohana_Config_Writer` interface.

e.g. for Database:

	class Kohana_Config_Database_Reader implements Kohana_Config_Reader
	class Kohana_Config_Database_Writer extends Kohana_Config_Database_Reader implements Kohana_Config_Writer

Although not enforced, the convention is that writers extend config readers.

To help maintain backwards compatability when loading config sources empty classes are provided for the db/file sources
which extends the source's reader/writer.

e.g.

	class Kohana_Config_File extends Kohana_Config_File_Reader
	class Kohana_Config_Database extends Kohana_Config_Database_Writer

## External requests

In Kohana 3.2, `Request_Client_External` now has three separate drivers to handle external requests;

 - `Request_Client_Curl` is the default driver, using the PHP Curl extension
 - `Request_Client_HTTP` uses the PECL HTTP extension
 - `Request_Client_Stream` uses streams native to PHP and requires no extensions. However this method is slower than the alternatives.

Unless otherwise specified, `Request_Client_Curl` will be used for all external requests. This can be changed for all external requests, or for individual requests.

To set an external driver across all requests, add the following to the `application/bootstrap.php` file;

    // Set all external requests to use PECL HTTP
    Request_Client_External::$client = 'Request_Client_HTTP';

Alternatively it is possible to set a specific client to an individual Request.

    // Set the Stream client to an individual request and
    // Execute
    $response = Request::factory('http://kohanaframework.org')
        ->client(new Request_Client_Stream)
        ->execute();

## HTTP cache control

Kohana 3.1 introduced HTTP cache control, providing RFC 2616 fully compliant transparent caching of responses. Kohana 3.2 builds on this moving all caching logic out of `Request_Client` into `HTTP_Cache`.

[!!] HTTP Cache requires the Cache module to be enabled in all versions of Kohana!

In Kohana 3.1, HTTP caching was enabled doing the following;

    // Apply cache to a request
    $request = Request::factory('foo/bar', Cache::instance('memcache'));

    // In controller, ensure response sets cache control,
    // this will cache the response for one hour
    $this->response->headers('cache-control', 
        'public, max-age=3600');

In Kohana 3.2, HTTP caching is enabled slightly differently;

    // Apply cache to request
    $request = Request::factory('foo/bar',
        HTTP_Cache::factory('memcache'));

    // In controller, ensure response sets cache control,
    // this will cache the response for one hour
    $this->response->headers('cache-control', 
        'public, max-age=3600');

## Controller Action Parameters

In 3.1, controller action parameters were deprecated. In 3.2, these behavior has been removed. If you had any code like:

	public function action_index($id)
	{
		// ... code
	}

You'll need to change it to:

	public function action_index()
	{
		$id = $this->request->param('id');

		// ... code
	}

## Form Class

If you used Form::open(), the default behavior has changed. It used to default to "/" (your home page), but now an empty parameter will default to the current URI.
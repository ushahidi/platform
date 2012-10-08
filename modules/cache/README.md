Kohana Cache library
====================

The cache library for Kohana 3 provides a simple interface to the most common cache solutions. Developers are free to add their own caching solutions that follow the cache design pattern defined within this module.

Supported cache solutions
-------------------------

Currently this module supports the following cache methods.

1. APC
2. Memcache
3. Memcached-tags (Supports tags)
4. SQLite (Supports tags)
5. File
6. Wincache

Planned support
---------------

In the near future, additional support for the following methods will be included.

1. Memcached

Introduction to caching
-----------------------

To use caching to the maximum potential, your application should be designed with caching in mind from the outset. In general, the most effective caches contain lots of small collections of data that are the result of expensive computational operations, such as searching through a large data set.

There are many different caching methods available for PHP, from the very basic file based caching to opcode caching in eAccelerator and APC. Caching engines that use physical memory over disk based storage are always faster, however many do not support more advanced features such as tagging.

Using Cache
-----------

To use Kohana Cache, download and extract the latest stable release of Kohana Cache from [Github](http://github.com/samsoir/kohana-cache). Place the module into your Kohana instances modules folder. Finally enable the module within the application bootstrap within the section entitled _modules_.

Quick example
-------------

The following is a quick example of how to use Kohana Cache. The example is using the SQLite driver.

	<?php
	// Get a Sqlite Cache instance  
	$mycache = Cache::instance('sqlite');
	
	// Create some data
	$data = array('foo' => 'bar', 'apples' => 'pear', 'BDFL' => 'Shadowhand');
	
	// Save the data to cache, with an id of test_id and a lifetime of 10 minutes
	$mycache->set('test_id', $data, 600);
	
	// Retrieve the data from cache
	$retrieved_data = $mycache->get('test_id');
	
	// Remove the cache item
	$mycache->delete('test_id');
	
	// Clear the cache of all stored items
	$mycache->delete_all();

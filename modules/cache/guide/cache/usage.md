# Kohana Cache usage

[Kohana_Cache] provides a simple interface allowing getting, setting and deleting of cached values. Two interfaces included in _Kohana Cache_ additionally provide _tagging_ and _garbage collection_ where they are supported by the respective drivers.

## Getting a new cache instance

Creating a new _Kohana Cache_ instance is simple, however it must be done using the [Cache::instance] method, rather than the traditional `new` constructor.

     // Create a new instance of cache using the default group
     $cache = Cache::instance();

The default group will use whatever is set to [Cache::$default] and must have a corresponding [configuration](cache.config) definition for that group.

To create a cache instance using a group other than the _default_, simply provide the group name as an argument.

     // Create a new instance of the memcache group
     $memcache = Cache::instance('memcache');

If there is a cache instance already instantiated then you can get it directly from the class member.

 [!!] Beware that this can cause issues if you do not test for the instance before trying to access it.

     // Check for the existance of the cache driver
     if (isset(Cache::$instances['memcache']))
     {
          // Get the existing cache instance directly (faster)
          $memcache = Cache::$instances['memcache'];
     }
     else
     {
          // Get the cache driver instance (slower)
          $memcache = Cache::instance('memcache');
     }

## Setting and getting variables to and from cache

The cache library supports scalar and object values, utilising object serialization where required (or not supported by the caching engine). This means that the majority or objects can be cached without any modification.

 [!!] Serialisation does not work with resource handles, such as filesystem, curl or socket resources.

### Setting a value to cache

Setting a value to cache using the [Cache::set] method can be done in one of two ways; either using the Cache instance interface, which is good for atomic operations; or getting an instance and using that for multiple operations.

The first example demonstrates how to quickly load and set a value to the default cache instance.

     // Create a cachable object
     $object = new stdClass;

     // Set a property
     $object->foo = 'bar';

     // Cache the object using default group (quick interface) with default time (3600 seconds)
     Cache::instance()->set('foo', $object);

If multiple cache operations are required, it is best to assign an instance of Cache to a variable and use that as below.

     // Set the object using a defined group for a defined time period (30 seconds)
     $memcache = Cache::instance('memcache');
     $memcache->set('foo', $object, 30);

#### Setting a value with tags

Certain cache drivers support setting values with tags. To set a value to cache with tags using the following interface.

     // Get a cache instance that supports tags
     $memcache = Cache::instance('memcachetag');

     // Test for tagging interface
     if ($memcache instanceof Cache_Tagging)
     {
          // Set a value with some tags for 30 seconds
          $memcache->set('foo', $object, 30, array('snafu', 'stfu', 'fubar'));
     }
     // Otherwise set without tags
     else
     {
          // Set a value for 30 seconds
          $memcache->set('foo', $object, 30);
     }

It is possible to implement custom tagging solutions onto existing or new cache drivers by implementing the [Cache_Tagging] interface. Kohana_Cache only applies the interface to drivers that support tagging natively as standard.

### Getting a value from cache

Getting variables back from cache is achieved using the [Cache::get] method using a single key to identify the cache entry.

     // Retrieve a value from cache (quickly)
     $object = Cache::instance()->get('foo');

In cases where the requested key is not available or the entry has expired, a default value will be returned (__NULL__ by default). It is possible to define the default value as the key is requested.

     // If the cache key is available (with default value set to FALSE)
     if ($object = Cache::instance()->get('foo', FALSE))
     {
          // Do something
     }
     else
     {
          // Do something else
     }

#### Getting values from cache using tags

It is possible to retrieve values from cache grouped by tag, using the [Cache::find] method with drivers that support tagging.

 [!!] The __Memcachetag__ driver does not support the `Cache::find($tag)` interface and will throw an exception.

     // Get an instance of cache
     $cache = Cache::instance('memcachetag');

     // Wrap in a try/catch statement to gracefully handle memcachetag
     try
     {
          // Find values based on tag
          return $cache->find('snafu');
     }
     catch (Cache_Exception $e)
     {
          // Handle gracefully
          return FALSE;
     }

### Deleting values from cache

Deleting variables is very similar to the getting and setting methods already described. Deleting operations are split into three categories:

 - __Delete value by key__. Deletes a cached value by the associated key.
 - __Delete all values__. Deletes all caches values stored in the cache instance.
 - __Delete values by tag__. Deletes all values that have the supplied tag. This is only supported by Memcached-Tag and Sqlite.

#### Delete value by key

To delete a specific value by its associated key:

     // If the cache entry for 'foo' is deleted
     if (Cache::instance()->delete('foo'))
     {
          // Cache entry successfully deleted, do something
     }

By default a `TRUE` value will be returned. However a `FALSE` value will be returned in instances where the key did not exist in the cache.

#### Delete all values

To delete all values in a specific instance:

     // If all cache items where deleted successfully
     if (Cache::instance()->delete_all())
     {
           // Do something
     }

It is also possible to delete all cache items in every instance:

     // For each cache instance
     foreach (Cache::$instances as $group => $instance)
     {
          if ($instance->delete_all())
          {
               var_dump('instance : '.$group.' has been flushed!');
          }
     }

#### Delete values by tag

Some of the caching drivers support deleting by tag. This will remove all the cached values that are associated with a specific tag. Below is an example of how to robustly handle deletion by tag.

     // Get cache instance
     $cache = Cache::instance();

     // Check for tagging interface
     if ($cache instanceof Cache_Tagging)
     {
           // Delete all entries by the tag 'snafu'
           $cache->delete_tag('snafu');
     }

#### Garbage Collection

Garbage Collection (GC) is the cleaning of expired cache entries. For the most part, caching engines will take care of garbage collection internally. However a few of the file based systems do not handle this task and in these circumstances it would be prudent to garbage collect at a predetermined frequency. If no garbage collection is executed, the resource storing the cache entries will eventually fill and become unusable.

When not automated, garbage collection is the responsibility of the developer. It is prudent to have a GC probability value that dictates how likely the garbage collection routing will be run. An example of such a system is demonstrated below.

     // Get a cache instance
     $cache_file = Cache::instance('file');

     // Set a GC probability of 10%
     $gc = 10;

     // If the GC probability is a hit
     if (rand(0,99) <= $gc and $cache_file instanceof Cache_GarbageCollect)
     {
          // Garbage Collect
          $cache_file->garbage_collect();
     }

# Interfaces

Kohana Cache comes with two interfaces that are implemented where the drivers support them:

 - __[Cache_Tagging] for tagging support on cache entries__
    - [Cache_MemcacheTag]
    - [Cache_Sqlite]
 - __[Cache_GarbageCollect] for garbage collection with drivers without native support__
    - [Cache_File]
    - [Cache_Sqlite]

When using interface specific caching features, ensure that code checks for the required interface before using the methods supplied. The following example checks whether the garbage collection interface is available before calling the `garbage_collect` method.

    // Create a cache instance
    $cache = Cache::instance();

    // Test for Garbage Collection
    if ($cache instanceof Cache_GarbageCollect)
    {
         // Collect garbage
         $cache->garbage_collect();
    }
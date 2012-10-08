# Mock objects

Sometimes when writing tests you need to test something that depends on an object being in a certain state.

Say for example you're testing a model - you want to make sure that the model is running the correct query, but you don't want it to run on a real database server.  You can create a mock database connection which responds in the way the model expects, but doesn't actually connect to a physical database.

PHPUnit has a built in mock object creator which can generate mocks for classes (inc. abstract ones) on the fly.  
It creates a class that extends the one you want to mock.  You can also tell PHPUnit to override certain functions to return set values / assert that they're called in a specific way.

## Creating an instance of a mock class

You create mocks from within testcases using the getMock() function, which is defined in `PHPUnit_Framework_TestCase` like so:

	    getMock($originalClassName, $methods = array(), array $arguments = array(), $mockClassName = '', $callOriginalConstructor = TRUE, $callOriginalClone = TRUE, $callAutoload = TRUE)

`$originalClassName`
: The name of the class that you want to mock

`$methods`
: The methods of $originalClassName that you want to mock.  
  You need to tell PHPUnit in advance because PHP doesn't allow you to extend an object once it's been initialised.

`$arguments`
: An array of arguments to pass to the mock's constructor

`$mockClassName`
: Allows you to specify the name that will be given to the mock

`$callOriginalConstructor`
: Should the mock call its parent's constructor automatically?

`$callOriginalClone`
: Should the mock call its parent's clone method?

Most of the time you'll only need to use the first two parameters, i.e.:

	$mock = $this->getMock('ORM');

`$mock` now contains a mock of ORM and can be handled as though it were a vanilla instance of `ORM`

	$mock = $this->getMock('ORM', array('check'));

`$mock` now contains a mock of ORM, but this time we're also mocking the check() method.

## Mocking methods

Assuming we've created a mock object like so:

	$mock = $this->getMock('ORM', array('check'));

We now need to tell PHPUnit how to mock the check function when its called.

### How many times should it be called?

You start off by telling PHPUnit how many times the method should be called by calling expects() on the mock object:

	$mock->expects($matcher);

`expects()` takes one argument, an invoker matcher which you can create using factory methods defined in `PHPUnit_Framework_TestCase`:

#### Possible invoker matchers:

`$this->any()`
: Returns a matcher that allows the method to be called any number of times

`$this->never()`
: Returns a matcher that asserts that the method is never called

`$this->once()`
: Returns a matcher that asserts that the method is only called once

`$this->atLeastOnce()`
: Returns a matcher that asserts that the method is called at least once

`$this->exactly($count)`
: Returns a matcher that asserts that the method is called at least `$count` times

`$this->at($index)`
: Returns a matcher that matches when the method it is evaluated for is invoked at the given $index.

In our example we want `check()` to be called once on our mock object, so if we update it accordingly:

	$mock = $this->getMock('ORM', array('check'));

	$mock->expects($this->once());

### What is the method we're mocking?

Although we told PHPUnit what methods we want to mock, we haven't actually told it what method these rules we're specifiying apply to.  
You do this by calling `method()` on the returned from `expects()`:

	$mock->expects($matcher)
		->method($methodName);

As you can probably guess, `method()` takes one parameter, the name of the method you're mocking.  
There's nothing very fancy about this function.

	$mock = $this->GetMock('ORM', array('check'));

	$mock->expects($this->once())
		->method('check');


### What parameters should our mock method expect?

There are two ways to do this, either

* Tell the method to accept any parameters
* Tell the method to accept a specific set of parameters

The former can be achieved by calling `withAnyParameters()` on the object returned from `method()`

	$mock->expects($matcher)
		->method($methodName)
		->withAnyParameters();

To only allow specific parameters you can use the `with()` method which accepts any number of parameters.  
The order in which you define the parameters is the order that it expects them to be in when called.

	$mock->expects($matcher)
		->method($methodName)
		->with($param1, $param2);

Calling `with()` without any parameters will force the mock method to accept no parameters.

PHPUnit has a fairly complex way of comparing parameters passed to the mock method with the expected values, which can be summarised like so - 

* If the values are identical, they are equal
* If the values are of different types they are not equal
* If the values are numbers they they are considered equal if their difference is equal to zero (this level of accuracy can be changed)
* If the values are objects then they are converted to arrays and are compared as arrays
* If the values are arrays then any sub-arrays deeper than x levels (default 10) are ignored in the comparision
* If the values are arrays and one contains more than elements that the other (at any depth up to the max depth), then they are not equal

#### More advanced parameter comparisions

Sometimes you need to be more specific about how PHPUnit should compare parameters, i.e. if you want to make sure that one of the parameters is an instance of an object, yet isn't necessarily identical to a particular instance.

In PHPUnit, the logic for validating objects and datatypes has been refactored into "constraint objects".  If you look in any of the assertX() methods you can see that they are nothing more than wrappers for associating constraint objects with tests.

If a parameter passed to `with()` is not an instance of a constraint object (one which extends `PHPUnit_Framework_Constraint`) then PHPUnit creates a new `IsEqual` comparision object for it.

i.e., the following methods produce the same result:

	->with('foo', 1);

	->with($this->equalTo('foo'), $this->equalTo(1));

Here are some of the wrappers PHPUnit provides for creating constraint objects:

`$this->arrayHasKey($key)`
: Asserts that the parameter will have an element with index `$key`

`$this->attribute(PHPUnit_Framework_Constraint $constraint, $attributeName)`
: Asserts that object attribute `$attributeName` of the parameter will satisfy `$constraint`, where constraint is an instance of a constraint (i.e. `$this->equalTo()`)

`$this->fileExists()`
: Accepts no parameters, asserts that the parameter is a path to a valid file (i.e. `file_exists() === TRUE`)

`$this->greaterThan($value)`
: Asserts that the parameter is greater than `$value`

`$this->anything()`
: Returns TRUE regardless of what the parameter is

`$this->equalTo($value, $delta = 0, $canonicalizeEOL = FALSE, $ignoreCase = False)`
: Asserts that the parameter is equal to `$value` (same as not passing a constraint object to `with()`)
: `$delta` is the degree of accuracy to use when comparing numbers. i.e. 0 means numbers need to be identical, 1 means numbers can be within a distance of one from each other
: If `$canonicalizeEOL` is TRUE then all newlines in string values will be converted to `\n` before comparision
: If `$ignoreCase` is TRUE then both strings will be converted to lowercase before comparision

`$this->identicalTo($value)`
: Asserts that the parameter is identical to `$value`

`$this->isType($type)`
: Asserts that the parameter is of type `$type`, where `$type` is a string representation of the core PHP data types

`$this->isInstanceOf($className)`
: Asserts that the parameter is an instance of `$className`

`$this->lessThan($value)`
: Asserts that the parameter is less than `$value`

`$this->objectHasAttribute($attribute)`
: Asserts that the paramater (which is assumed to be an object) has an attribute `$attribute`

`$this->matchesRegularExpression($pattern)`
: Asserts that the parameter matches the PCRE pattern `$pattern` (using `preg_match()`)

`$this->stringContains($string, $ignoreCase = FALSE)`
: Asserts that the parameter contains the string `$string`.  If `$ignoreCase` is TRUE then a case insensitive comparision is done

`$this->stringEndsWith($suffix)`
: Asserts that the parameter ends with `$suffix` (assumes parameter is a string)

`$this->stringStartsWith($prefix)`
: Asserts that the parameter starts with `$prefix` (assumes parameter is a string)

`$this->contains($value)`
: Asserts that the parameter contains at least one value that is identical to `$value` (assumes parameter is array or `SplObjectStorage`)

`$this->containsOnly($type, $isNativeType = TRUE)`
: Asserts that the parameter only contains items of type `$type`. `$isNativeType` should be set to TRUE when `$type` refers to a built in PHP data type (i.e. int, string etc.) (assumes parameter is array)


There are more constraint objects than listed here, look in `PHPUnit_Framework_Assert` and `PHPUnit/Framework/Constraint` if you need more constraints.

If we continue our example, we have the following:  
	
	$mock->expects($this->once())
		->method('check')
		->with();

So far PHPUnit knows that we want the `check()` method to be called once, with no parameters.  Now we just need to get it to return something...

### What should the method return?

This is the final stage of mocking a method.

By default PHPUnit can return either

* A fixed value
* One of the parameters that were passed to it
* The return value of a specified callback

Specifying a return value is easy, just call `will()` on the object returned by either `method()` or `with()`.

The function is defined like so:

	public function will(PHPUnit_Framework_MockObject_Stub $stub)

PHPUnit provides some MockObject stubs out of the box, you can access them via (when called from a testcase):

`$this->returnValue($value)`
: Returns `$value` when the mocked method is called

`$this->returnArgument($argumentIndex)`
: Returns the `$argumentIndex`th argument that was passed to the mocked method

`$this->returnCallback($callback)`
: Returns the value of the callback, useful for more complicated mocking.  
: `$callback` should a valid callback (i.e. `is_callable($callback) === TRUE`).  PHPUnit will pass the callback all of the parameters that the mocked method was passed, in the same order / argument index (i.e. the callback is invoked by `call_user_func_array()`).
: You can usually create the callback in your testcase, as long as doesn't begin with "test"

Obviously if you really want to you can create your own MockObject stub, but these three should cover most situations.

Updating our example gives:

	$mock->expects($this->once())
		->method('check')
		->with()
		->will($this->returnValue(TRUE));


And we're done!

If you now call `$mock->check()` the value TRUE should be returned.

If you don't call a mocked method and PHPUnit expects it to be called then the test the mock was generated for will fail.


<!--
### What about if the mocked method should change everytime its called?

-->

<?php

/**
 * Ushahidi Platform Verified Parser Trait
 *
 * Defines a convention of method names that can be used to verify and provide
 * default values for some kind of expected input. These methods can be defined
 * in another trait for reuse, or defined in a concrete class.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits\Parser;

// cannot use until it stops extending ValidatorException
// use Ushahidi\Core\Exception\ParserException;

trait VerifiedParser
{
	/**
	 * Checks that a value exists in a given list. If not, throws a parser exception.
	 * @param  String $name  parameter identifier
	 * @param  Mixed  $value actual parameter
	 * @param  Array  $allow possible values
	 * @throws Ushahidi\Core\Exceptions\ParserException
	 * @return void
	 */
	protected function isInArray($name, $value, Array $allow)
	{
		if (!in_array($value, $allow)) {
			throw new \InvalidArgumentException(
				sprintf('%s parameter must be one of: %s', $name, implode(', ', $allow))
			);
		}
	}

	/**
	 * Defines what is considered an "empty" value. The default values are:
	 * empty strings, boolean false, and null.
	 * @return Array
	 */
	protected function getEmptyValues()
	{
		return ["", false, null];
	}

	/**
	 * Given an array of input and a list of wanted keys, verify the input
	 * value for the key or return the default value, using specific methods.
	 *
	 * Given a wanted key of "username", check if the input value is empty.
	 * If so, use the getDefaultUsername method to get the default value.
	 * If not, use the getValidUsername method to get a verified value.
	 *
	 * From a programming standpoint:
	 *
	 *   if input[foo]
	 *     foo = getValidFoo(input[foo])
	 *   else
	 *     foo = getDefaultFoo()
	 *
	 * By this convention, any input key can be captured and validated by defining
	 * getValid* and getDefault* methods.
	 *
	 * @param  Array  $input  external input
	 * @param  Array  $wanted list of keys
	 * @return Array
	 */
	protected function getVerifiedInput(Array $input, Array $wanted)
	{
		$empty_values = $this->getEmptyValues();

		$output = [];
		foreach ($wanted as $key) {
			if (isset($input[$key]) && !in_array($input[$key], $empty_values)) {
				$method = 'getValid' . ucfirst($key);
				$output[$key] = $this->$method($input[$key]);
			} else {
				$method = 'getDefault' . ucfirst($key);
				$output[$key] = $this->$method();
			}
		}
		return $output;
	}
}

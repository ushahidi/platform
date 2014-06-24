<?php

/**
 * Ushahidi Platform Data Input for Use Cases
 *
 * Works very similar to "struct" objects in other languages (eg Ruby),
 * but requires that all possible properties are pre-defined, to ensure
 * a highly predictable object state.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi;

use Ushahidi\Traits\ArrayExchange;

abstract class Data
{
	use ArrayExchange {
		asArray as private asArraySimple;
	}

	/**
	 * @var Array allowed keys that were in the input data
	 */
	private static $defined_input_keys = [];

	/**
	 * Stores what (allowed) keys were defined by input data.
	 * @param  Array  $data  raw input
	 * @return void
	 */
	public function __construct(Array $data)
	{
		// Get the (empty) array that currently exists, this tells us what
		// properties are allowed in this object.
		$allowed = $this->asArraySimple();

		// Using the allowed properties, determine which of them are present
		// in the given input data...
		$defined = array_keys(array_intersect_key($data, $allowed));

		// ... and store those values for delta comparison.
		self::$defined_input_keys[$this->getObjectId()] = $defined;

		$this->setData($data);
	}

	/**
	 * Clears what (allowed) keys were defined by input data.
	 * @return void
	 */
	public function __destruct()
	{
		unset(self::$defined_input_keys[$this->getObjectId()]);
	}

	/**
	 * Get the unique identity of the object instance.
	 * @return string
	 */
	final private function getObjectId()
	{
		return spl_object_hash($this);
	}

	/**
	 * Get all values in the current object, reducing the result to defined input.
	 * @return Array
	 */
	public function asArray()
	{
		// Get defined properties, flipping the list of keys into an associative array...
		$defined = array_flip(self::$defined_input_keys[$this->getObjectId()]);

		// ... and use it to reduce the values to what was actually input.
		$values = array_intersect_key($this->asArraySimple(), $defined);

		return $values;
	}

	/**
	 * Compare with some existing data and get the delta between the two.
	 * Only values that were present in the input data will be returned!
	 * @param  Array  $compare  existing data
	 * @return Data
	 */
	public function getDifferent(Array $compare)
	{
		// Get the difference of current data and comparison. If not all properties
		// were defined in input, this will contain false positive (empty) values.
		$delta = array_diff_assoc($this->asArray(), $compare);

		return new static($delta);
	}
}


<?php

/**
 * Ushahidi Platform Sorting Parser Trait
 *
 * Parses "orderby", "order", "limit", and "offset" parameters.
 * Allows customizing the default and allowed values for each.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Traits\Parser;

trait SortingParser
{
	// Uses VerifiedParser for array checking and param checking conventions.
	use VerifiedParser;

	/**
	 * Gets a list of the sorting parameters by name.
	 * @return Array
	 */
	public function getSortingParams()
	{
		return [
			'orderby',
			'order',
			'limit',
			'offset',
			];
	}

	/**
	 * Gets the default "order by" key.
	 * @return string
	 */
	private function getDefaultOrderby()
	{
		return 'id'; // by id
	}

	/**
	 * Gets a list of allowed "order by" keys.
	 * @return Array
	 */
	private function getAllowedOrderby()
	{
		return [$this->getDefaultOrderby()];
	}

	/**
	 * Gets the verified input for an "order" direction.
	 * Will throw an exception if the input is invalid.
	 * @param  String $orderby
	 * @return String
	 * @throws Ushahidi\Exception\ParserException
	 */
	private function getValidOrderby($orderby)
	{
		$orderby = strtolower($orderby);
		$allow = $this->getAllowedOrderby();

		$this->isInArray('orderby', $orderby, $allow);

		return $orderby;
	}

	/**
	 * Gets the default "order" direction.
	 * @return String
	 */
	private function getDefaultOrder()
	{
		return 'desc'; // newest first
	}

	/**
	 * Gets the list of allowed "order" directions.
	 * @return Array
	 */
	private function getAllowedOrder()
	{
		return ['asc', 'desc'];
	}

	/**
	 * Gets the verified input for an "order" direction.
	 * Will throw an exception if the input is invalid.
	 * @param  String $order
	 * @return String
	 * @throws Ushahidi\Exception\ParserException
	 */
	private function getValidOrder($order)
	{
		$order = strtolower($order);
		$allow = $this->getAllowedOrder();

		$this->isInArray('order', $order, $allow);

		return $order;
	}

	/**
	 * Gets the default "limit" count.
	 * @return Integer
	 */
	private function getDefaultLimit()
	{
		return 100;
	}

	/**
	 * Gets the maximum "limit" count.
	 * @return Integer
	 */
	private function getMaxLimit()
	{
		return 1000;
	}

	/**
	 * Gets the verified input for an "limit" count.
	 * Ensures the value is less than the maximum allowed.
	 * @param  Integer $limit
	 * @return Integer
	 */
	private function getValidLimit($limit)
	{
		// Prevent overloading storage by setting a ceiling on the limit.
		return min($this->getMaxLimit(), (int) $limit);
	}

	/**
	 * Gets the default "offset" count.
	 * @return Integer
	 */
	private function getDefaultOffset()
	{
		return 0;
	}

	/**
	 * Gets the verified input for an "offset" count.
	 * Simply casts the value to an integer.
	 * @param  Integer $offset
	 * @return Integer
	 */
	private function getValidOffset($offset)
	{
		return abs((int) $offset);
	}

	/**
	 * Convenience method for getting all of the verified sorting parameters.
	 * Can be used during the concrete parser's invocation.
	 * @param  Array $input
	 * @return Array
	 */
	protected function getSorting(Array $input)
	{
		return $this->getVerifiedInput($input, $this->getSortingParams());
	}
}

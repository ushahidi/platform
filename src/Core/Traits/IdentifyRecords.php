<?php

/**
 * Ushahidi Identify Records Trait
 *
 * Gives objects two methods:
 *
 * - `setIdentifiers(Array $identifiers)`
 * - `getIdentifier($name, $default)`
 *
 * Used to set conditions for identifying unique records.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

trait IdentifyRecords
{
	/**
	 * @var Array
	 */
	protected $identifiers = [];

	/**
	 * Set parameters that can be used to uniquely identify a **single** entity:
	 *
	 *     $obj->setIdentifiers([
	 *         'id' => 5,
	 *     ]);
	 *
	 * @param  Array $identifiers
	 * @return $this
	 */
	public function setIdentifiers(Array $identifiers)
	{
		$this->identifiers = $identifiers;
		return $this;
	}

	/**
	 * Get a parameter by name. A default value can be provided, which will be
	 * returned if the parameter does not exist. If no default is provided, and the
	 * parameter does not exist, an exception will be thrown.
	 *
	 *     // Get a required parameter
	 *     $id = $this->getIdentifier('id');
	 *
	 *     // Get an optional parameter, with a default
	 *     $parent = $this->getIdentifier('parent_id', false);
	 *
	 * @throws InvalidArgumentException
	 * @param  String $name
	 * @param  Mixed  $default
	 * @return Mixed
	 */
	protected function getIdentifier($name, $default = null)
	{
		if (!isset($this->identifiers[$name])) {
			if (!isset($default)) {
				throw new \InvalidArgumentException(sprintf(
					'Identifier %s has not been declared, available options are: %s',
					$name,
					implode(', ', array_keys($this->identifiers))
				));
			}
			return $default;
		}
		return $this->identifiers[$name];
	}
}

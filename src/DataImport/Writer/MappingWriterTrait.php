<?php

/**
 * Mapping Writer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataImport\Writer;

trait MappingWriter
{

	protected $originalIdentifier;
	protected $map;

	// @todo Define interface
	public function setOriginalIdentifier($key)
	{
		$this->originalIdentifier = $key;
	}

	// @todo Define interface
	// @todo should this receive entire item? (probably not since we compare parent ID)
	public function getMappedId($originalId)
	{
		return isset($this->map[$originalId]) ? $this->map[$originalId] : false;
	}

	public function getMap()
	{
		return $this->map;
	}

	/**
	 * Record mapped id for item
	 * @param Array  $item
	 * @param Int    $newId
	 */
	protected function setMappedId($item, $newId)
	{
		if ($this->originalIdentifier && $item[$this->originalIdentifier]) {
			$this->map[$item[$this->originalIdentifier]] = $newid;
		}
	}
}

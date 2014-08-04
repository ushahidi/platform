<?php
/**
 * Implement ACL_Resource_Interface for Ushahidi\Entities
 *
 * Proxies properties and resource name to Entity object
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity;

class Ushahidi_EntityACLResourceProxy implements ACL_Resource_Interface {

	protected $entity;

	/**
	 * @param Entity $entity
	 */
	public function __construct(Entity $entity)
	{
		$this->entity = $entity;
	}

	// ACL_Resource_Interface
	public function get_resource_id()
	{
		return $this->entity->getResource();
	}

	/**
	 * Proxy property access to entity object
	 */
	public function __get($name)
	{
		if (property_exists($this->entity, $name))
		{
			return $this->entity->$name;
		}
		else
		{
			throw new InvalidArgumentException("Property $name is not defined");
		}
	}

	/**
	 * Proxy isset checks to entity object
	 */
	public function __isset($name)
	{
		return isset($this->entity->$name);
	}
}
<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Role Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\Role;
use Ushahidi\Entity\RoleRepository;

class Ushahidi_Repository_Role extends Ushahidi_Repository implements RoleRepository
{
	//Ushahidi_Repository
	protected function getTable()
	{
		return 'roles';
	}

	protected function getEntity(Array $data = null)
	{
		return new Role($data);
	}

	public function get($name)
	{
		return $this->getEntity($this->selectOne(compact('name')));
	}

	// RoleRepository
	public function doRolesExist($name)
	{
		$found = (int) $this->selectCount(compact('name'));
		$count = count($name);
		return $found === $count;
		
	}

}

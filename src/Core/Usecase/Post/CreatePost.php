<?php

/**
 * Ushahidi Platform Entity Create Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Usecase\CreateUsecase;
use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;

class CreatePost extends CreateUsecase
{
	// - GenerateValues fills in auto generated values
	use GenerateValues;

	protected $type = 'report';
	protected $parent_id;

	public function interact(Data $input)
	{
		$input = $this->fillGeneratedValues($input);

		return parent::interact($input);
	}

	/**
	 * @param  string $type
	 * @return void
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @param  int $parent_id
	 * @return void
	 */
	public function setParent($parent_id)
	{
		$this->parent_id = $parent_id;
	}

	/**
	 * Verifies the current user is allowed read access on $entity
	 *
	 * @param  Entity  $entity
	 * @return void
	 * @throws AuthorizerException
	 */
	protected function verifyReadAuth(Entity $entity)
	{
		// Throwing an error w/o read permissions breaks on anonymous users
		// Maybe should just return a 204 (No Content)? or 202 (Accepted)?
		// $this->verifyAuth($entity, 'read');
	}

	/**
	 * Verifies the current user is allowed update access on $entity
	 *
	 * @param  Entity  $entity
	 * @param  Data    $input
	 * @return void
	 * @throws AuthorizerException
	 */
	protected function verifyCreateAuth(Entity $entity, Data $input)
	{
		$this->verifyAuth($entity, 'create');

		// if changing user id or author info
		if ($input->user_id !== $this->auth->getUserId()) {
			$this->verifyAuth($entity, 'change_user');
		}
	}
}

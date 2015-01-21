<?php

/**
 * Ushahidi Validator Tool Trait
 *
 * Gives objects a method for storing an validator instance.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Exception\ValidatorException;

trait ValidatorTrait
{
	/**
	 * @var Validator
	 */
	protected $valid;

	/**
	 * @param  Validator $valid
	 * @return void
	 */
	public function setValidator(Validator $valid)
	{
		$this->valid = $valid;
		return $this;
	}

	/**
	 * Verify that the given entity is valid.
	 *
	 * @param  Entity $entity
	 * @return void
	 * @throws ValidatorException
	 */
	protected function verifyValid(Entity $entity)
	{
		if (!$this->valid->check($entity)) {
			throw new ValidatorException(sprintf(
				'Failed to validate %s entity',
				$entity->getResource()
			), $this->valid->errors());
		}
	}
}

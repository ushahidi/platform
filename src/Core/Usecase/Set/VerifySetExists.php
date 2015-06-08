<?php

/**
 * Ushahidi Platform Verify Set Exists for Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Set;

use Ushahidi\Core\Entity;

trait verifySetExists
{

	/**
	 * Checks that the set exists.
	 * @param  Data $input
	 * @return void
	 */
	protected function verifySetExists()
	{
		// Ensure that the set exists.
		$set = $this->getSetRepository()->get($this->getRequiredIdentifier('set_id'));
		$this->verifyEntityLoaded($set, $this->identifiers);
	}

	// Usecase
	public function interact()
	{
		$this->verifySetExists();
		return parent::interact();
	}

	// IdentifyRecords
	abstract protected function getRequiredIdentifier($name);

	// VerifyEntityLoaded
	abstract protected function verifyEntityLoaded(Entity $entity, $lookup);

	// SetRepositoryTrait
	abstract public function getSetRepository();
}

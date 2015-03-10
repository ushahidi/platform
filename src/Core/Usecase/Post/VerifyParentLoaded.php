<?php

/**
 * Ushahidi Platform Verify Parent Posts exists for Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Traits\IdentifyRecords;
use Ushahidi\Core\Traits\VerifyEntityLoaded;

use Ushahidi\Core\Exception\NotFoundException;

trait VerifyParentLoaded
{
	// For parent check:
	// - IdentifyRecords
	// - VerifyEntityLoaded
	use IdentifyRecords,
		VerifyEntityLoaded;

	/**
	 * Checks that the parent exists.
	 * @param  Data $input
	 * @return void
	 */
	protected function verifyParentExists()
	{
		if ($parent_id = $this->getIdentifier('parent_id')) {
			// Ensure that the parent exists.
			$parent = $this->repo->get($parent_id);
			$this->verifyEntityLoaded($parent, $this->identifiers);
			// Ensure that we are allowed to access the parent
			$this->verifyReadAuth($parent);
		}
	}

	// Usecase
	public function interact()
	{
		$this->verifyParentExists();
		return parent::interact();
	}
}

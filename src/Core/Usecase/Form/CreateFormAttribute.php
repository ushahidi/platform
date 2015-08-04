<?php

/**
 * Ushahidi Platform Create Form Attribute Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Form;

use Ushahidi\Core\Usecase\CreateUsecase;
use Ushahidi\Core\Traits\IdentifyRecords;
use Ushahidi\Core\Traits\VerifyEntityLoaded;

class CreateFormAttribute extends CreateUsecase
{
	// - VerifyStageLoaded for checking that the stage exists
	use VerifyStageLoaded;

	// For form check:
	// - IdentifyRecords
	// - VerifyEntityLoaded
	use IdentifyRecords,
		VerifyEntityLoaded;

	// CreateUsecase
	protected function getEntity()
	{
		$entity = parent::getEntity();

		$this->verifyStageExists($entity);

		return $entity;
	}
}

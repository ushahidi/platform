<?php

/**
 * Ushahidi Platform Search Form Stage Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Form;

use Ushahidi\Core\Usecase\SearchUsecase;
use Ushahidi\Core\Traits\IdentifyRecords;
use Ushahidi\Core\Traits\VerifyEntityLoaded;

class SearchFormStage extends SearchUsecase
{
	// - VerifyFormLoaded for checking that the form exists
	use VerifyFormLoaded;

	// For form check:
	// - IdentifyRecords
	// - VerifyEntityLoaded
	use IdentifyRecords,
		VerifyEntityLoaded;

	protected function verifyFormExists()
	{
		//noop since a form is not required for searches
	}
}

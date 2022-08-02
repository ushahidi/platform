<?php

/**
 * Ushahidi Platform Search Form Attribute Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Usecase\Form;

use Ushahidi\App\V3\Usecase\SearchUsecase;
use Ushahidi\App\V3\Usecase\Concerns\IdentifyRecords;
use Ushahidi\App\V3\Usecase\Concerns\VerifyFormLoaded;
use Ushahidi\App\V3\Usecase\Concerns\VerifyEntityLoaded;

class SearchFormAttribute extends SearchUsecase
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
        if ($identifier = $this->getIdentifier('form_id')) {
            $form = $this->form_repo->get($identifier);
            $this->verifyEntityLoaded($form, $this->identifiers);
        }
    }
}

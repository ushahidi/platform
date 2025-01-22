<?php

/**
 * Ushahidi Platform Verify Parent Posts exists for Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Concerns;

use Ushahidi\Core\Usecase\Concerns\IdentifyRecords;
use Ushahidi\Core\Usecase\Concerns\VerifyEntityLoaded;

trait VerifyParentLoaded
{
    // For parent check:
    // - IdentifyRecords
    // - VerifyEntityLoaded
    use IdentifyRecords,
        VerifyEntityLoaded;

    /**
     * Checks that the parent exists.
     *
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

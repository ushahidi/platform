<?php

/**
 * Repository for TargetedSurveyState
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;
use Ushahidi\Core\Entity\Repository\EntityExists;
use Ushahidi\Core\Usecase\UpdateRepository;

interface TargetedSurveyStateRepository extends
    EntityGet,
    EntityExists,
    UpdateRepository
{

    /**
     * @param string  $contact
     * @return boolean
     */
    public function getActiveByContactId($contact_id);

    /**
     * @param string  $contact
     * @return boolean
     */
    public function isContactInActiveTargetedSurveyAndReceivedMessage($contact);
}

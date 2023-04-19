<?php

/**
 * Repository for Contacts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Repository\EntityCreate;
use Ushahidi\Contracts\Repository\EntityCreateMany;
use Ushahidi\Contracts\Repository\EntityGet;
use Ushahidi\Contracts\Repository\EntityExists;
use Ushahidi\Contracts\Repository\CreateRepository;

interface ContactRepository extends
    EntityGet,
    EntityCreate,
    EntityCreateMany,
    EntityExists,
    CreateRepository
{
    /**
     * @param string  $contact
     * @param string  $type
     *
     * @return \Ushahidi\Contracts\Entity
     */
    public function getByContact($contact, $type);

    /**
     * Get all contacts that can be notified and filter by collection or saved search.
     * @param int $set_id collection or saved search id to filter by
     * @param bool|int $limit false to fetch all contacts
     * @param int $offset
     */
    public function getNotificationContacts($set_id, $limit = false, $offset = 0);
}

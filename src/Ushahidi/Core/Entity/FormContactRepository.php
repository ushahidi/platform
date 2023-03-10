<?php

/**
 * Repository for Form Contacts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Repository\EntityGet;
use Ushahidi\Contracts\Repository\EntityExists;

interface FormContactRepository extends
    EntityGet,
    EntityExists
{

    /**
     * @param  int $form_id
     * @return [Ushahidi\Core\Entity\FormContact, ...]
     */
    public function getByForm($form_id);

    /**
     * @param  int $contact_id
     * @param  int $form_id
     * @return [Ushahidi\Core\Entity\FormContact, ...]
     */
    public function existsInFormContact($contact_id, $form_id);

    /**
     * @param  [Ushahidi\Core\Entity\FormContact, ...]  $entities
     * @return [Ushahidi\Core\Entity\FormContact, ...]
     */
    public function updateCollection(array $entities);
}

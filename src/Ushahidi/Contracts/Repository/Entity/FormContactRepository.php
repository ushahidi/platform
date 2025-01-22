<?php

/**
 * Repository for Form Contacts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Entity;

use Ushahidi\Contracts\EntityGet;
use Ushahidi\Contracts\EntityExists;

interface FormContactRepository extends
    EntityGet,
    EntityExists
{

    /**
     * @param  int $form_id
     * @return [Ushahidi\Contracts\Repository\Entity\FormContact, ...]
     */
    public function getByForm($form_id);

    /**
     * @param  int $contact_id
     * @param  int $form_id
     * @return [Ushahidi\Contracts\Repository\Entity\FormContact, ...]
     */
    public function existsInFormContact($contact_id, $form_id);

    /**
     * @param  [Ushahidi\Contracts\Repository\Entity\FormContact, ...]  $entities
     * @return [Ushahidi\Contracts\Repository\Entity\FormContact, ...]
     */
    public function updateCollection(array $entities);
}

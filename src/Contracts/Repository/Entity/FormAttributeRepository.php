<?php

/**
 * Repository for Form Attributes
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html
 *             GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Entity;

use Ushahidi\Contracts\EntityGet;
use Ushahidi\Contracts\EntityExists;

interface FormAttributeRepository extends
    EntityGet,
    EntityExists
{
    /**
     * @param  string $key
     * @param  int    $form_id
     * @param  boolean $include_no_form  Include attributes with null form_id
     * @return Ushahidi\Contracts\Repository\Entity\FormAttribute
     */
    public function getByKey($key, $form_id = null, $include_no_form = false);

    /**
     * @param  int $form_id
     * @return [Ushahidi\Contracts\Repository\Entity\FormAttribute, ...]
     */
    public function getByForm($form_id);

    /**
     * @param  int $form_id
     * @return [Ushahidi\Contracts\Repository\Entity\FormAttribute, ...]
     */
    public function getFirstNonDefaultByForm($form_id);

    /**
     * @return [Ushahidi\Contracts\Repository\Entity\FormAttribute, ...]
     */
    public function getAll();

    /**
     * @param  int $stage_id
     * @return [Ushahidi\Contracts\Repository\Entity\FormAttribute, ...]
     */
    public function getRequired($stage_id);

    /**
     * @param  string  $key
     * @return boolean
     */
    public function isKeyAvailable($key);

    /**
     * @param  array $include_attributes
     * @return \Ushahidi\Core\Entity\FormAttribute[]
     */
    public function getExportAttributes(array $include_attributes = null);

    /**
     * @param int $form_id
     * @return Entity|FormAttribute
     */
    public function getNextByFormAttribute($last_attribute_id);
}

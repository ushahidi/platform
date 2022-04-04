<?php

/**
 * Repository for Form Stages
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts\Repository\Entity;

use Ushahidi\Contracts\EntityGet;
use Ushahidi\Contracts\EntityExists;

interface FormStageRepository extends
    EntityGet,
    EntityExists
{

    /**
     * @param  int $form_id
     * @return [Ushahidi\Contracts\Repository\Entity\FormStage, ...]
     */
    public function getByForm($form_id);

    /**
     * @param  int $id
     * @param  int $form_id
     * @return [Ushahidi\Contracts\Repository\Entity\FormStage, ...]
     */
    public function existsInForm($id, $form_id);

    /**
     * Get required stages for form
     *
     * @param  int $form_id
     * @return [Ushahidi\Contracts\Repository\Entity\FormAttribute, ...]
     */
    public function getRequired($form_id);

    /**
     * Get 'post' type stage for form
     *
     * @param  int $form_id
     * @return Ushahidi\Contracts\Repository\Entity\FormAttribute
     */
    public function getPostStage($form_id);
}

<?php

/**
 * Repository for Form Stages
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Repository\EntityGet;
use Ushahidi\Contracts\Repository\EntityExists;
use Ushahidi\Contracts\Repository\EntityCreate;

interface FormStageRepository extends
    EntityGet,
    EntityExists,
    EntityCreate
{
    /**
     * @param  int $form_id
     * @return \Ushahidi\Core\Entity\FormStage[]
     */
    public function getByForm($form_id);

    /**
     * @param  int $id
     * @param  int $form_id
     * @return \Ushahidi\Core\Entity\FormStage[]
     */
    public function existsInForm($id, $form_id);

    /**
     * Get required stages for form
     *
     * @param  int $form_id
     * @return \Ushahidi\Core\Entity\FormStage[]
     */
    public function getRequired($form_id);

    /**
     * Get 'post' type stage for form
     *
     * @param  int $form_id
     * @return \Ushahidi\Core\Entity\FormStage
     */
    public function getPostStage($form_id);
}

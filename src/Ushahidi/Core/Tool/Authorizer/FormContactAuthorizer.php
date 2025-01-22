<?php

/**
 * Ushahidi Form Stage Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Authorizer;
use Ushahidi\Core\Concerns\AdminAccess;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Contracts\Repository\Entity\FormRepository;

/** The `FormContactAuthorizer` class is responsible
 for access checks on `Contacts` that are created for a targetted survey
**/
class FormContactAuthorizer implements Authorizer
{
    // The access checks are run under the context of a specific user
    use UserContext;
    use AdminAccess;

    // It requires a `FormRepository` to load the owning form.
    protected $form_repo;

    // It requires a `FormAuthorizer` to check privileges against the owning form.
    protected $form_auth;

    /**
     * @param FormRepository $form_repo
     */
    public function __construct(FormRepository $form_repo, FormAuthorizer $form_auth)
    {
        $this->form_repo = $form_repo;
        $this->form_auth = $form_auth;
    }

    /* Authorizer */
    public function isAllowed(Entity $entity, $privilege)
    {
        $form = $this->getForm($entity);

        // All access is based on the form itself, not the stage.
        if (!$this->form_auth->isAllowed($form, $privilege)) {
            return false;
        }
        return $this->isUserAdmin($this->getUser());
    }

    /* Authorizer */
    public function getAllowedPrivs(Entity $entity)
    {
        $form = $this->getForm($entity);

        // All access is based on the form itself, not the stage.
        return $this->form_auth->getAllowedPrivs($form);
    }

    /**
     * Get the form associated with this stage.
     * @param  Entity $entity
     *
     * @return Entity
     */
    protected function getForm(Entity $entity)
    {
        return $this->form_repo->get($entity->form_id);
    }
}

<?php

/**
 * Ushahidi Platform Export Job Create Use Case
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Platform
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\HXL;

use Ushahidi\Core\Entity\HXL\HXLFormAttributeHXLAttributeTag;
use Ushahidi\Core\Usecase\CreateUsecase;

class CreateHXLHeadingRow extends CreateUsecase
{
    // Usecase
    public function interact()
    {
        // Fetch a default entity and apply the payload...
        $entity = $this->getEntity();
        // ... verify that the entity can be created by the current user
        $this->verifyCreateAuth($entity);

        // ... verify that the entity is in a valid state
        $this->verifyValid($entity);
        // get heading row to map hxl attributes and tags to form attributes
        $hxl_heading_row = $entity->hxl_heading_row;
        // ... persist the new entity
        $id = $this->repo->create($entity);

        // ... get the newly created entity
        $entity = $this->getCreatedEntity($id);
        // ... check that the entity can be read by the current user
        if ($this->auth->isAllowed($entity, 'read')) {
            // ... and either return the formatted entity
            return $this->formatter->__invoke($entity);
        } else {
            // ... or just return nothing
            return;
        }
    }
}

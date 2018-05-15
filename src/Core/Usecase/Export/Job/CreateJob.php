<?php

/**
 * Ushahidi Platform Export Job Create Use Case
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Platform
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Export\Job;

use Ushahidi\Core\Entity\HXL\HXLFormAttributeHXLAttributeTag;
use Ushahidi\Core\Entity\HXL\HXLFormAttributeHXLAttributeTagRepository;
use Ushahidi\Core\Usecase\CreateUsecase;

class CreateJob extends CreateUsecase
{
    protected $form_attribute_hxl_repository;

    public function setFormAttributeHxlRepository(HXLFormAttributeHXLAttributeTagRepository $repo)
    {
        $this->form_attribute_hxl_repository = $repo;
    }


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
        if ($entity->getId()) {
            foreach ($hxl_heading_row as $heading_row) {
                var_dump($heading_row);
                $entity_hxl = new HXLFormAttributeHXLAttributeTag();
                $entity_hxl->setState($heading_row);
                $entity_hxl->setState(['export_job_id' => $entity->getId()]);
                $this->form_attribute_hxl_repository->create($entity_hxl);
            }
        }
        // ... check that the entity can be read by the current user
        if ($this->auth->isAllowed($entity, 'read')) {
            // ... and either return the formatted entity
            return $this->formatter->__invoke($entity);
        } else {
            // ... or just return nothing
            return;
        }
    }
    protected function getEntity()
    {
        $entity = parent::getEntity();

        // Add user id if this is not provided
        // TODO: throw this away
        if (empty($entity->user_id) && $this->auth->getUserId()) {
            $entity->setState(['user_id' => $this->auth->getUserId()]);
        }

        // Default status filter to 'all' if not provided
        if (empty($entity->filters['status'])) {
            $filters = $entity->filters;
            $filters['status'] = ['all'];
            $entity->setState(['filters' => $filters]);
        }
        return $entity;
    }
}

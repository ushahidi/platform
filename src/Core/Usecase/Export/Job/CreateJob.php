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
use Ushahidi\Core\Usecase\Export\Job\CreateHXLHeadingRow;

class CreateJob extends CreateUsecase
{
    protected $form_attribute_hxl_repository;
    protected $create_hxl_heading_row;
    public function setFormAttributeHxlRepository(HXLFormAttributeHXLAttributeTagRepository $repo)
    {
        $this->form_attribute_hxl_repository = $repo;
    }

    public function setCreateHXLHeadingRowUsecase($usecase)
    {
        $this->create_hxl_heading_row = $usecase;
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
        // if there are items in $hxl_heading_row and the entity was created,
        // create the hxl tags for each attribute
        if ($entity->getId() && is_array($hxl_heading_row)) {
            $this->createHxlHeadingTags($hxl_heading_row, $entity);
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

    /**
     * @param $hxl_heading_row
     * @param $entity
     * call the usecase to create the form attribute hxl tags for each heading row item
     */
    private function createHxlHeadingTags($hxl_heading_row, $entity)
    {
        foreach ($hxl_heading_row as $heading_row) {
            if (isset($heading_row['hxl_tag_id'])) {
                $heading_row['export_job_id'] = $entity->getId();
                $this->create_hxl_heading_row
                    ->get('form_attribute_hxl_attribute_tag', 'create')
                    ->setPayload($heading_row)
                    ->interact();
            }
        }
    }

    /**
     * @return \Ushahidi\Core\Entity
     */
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

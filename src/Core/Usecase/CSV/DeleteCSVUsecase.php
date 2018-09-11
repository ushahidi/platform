<?php

/**
 * Ushahidi Platform Delete Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\CSV;

use Ushahidi\Core\Tool\Uploader;
use Ushahidi\Core\Usecase\DeleteUsecase;

class DeleteCSVUsecase extends DeleteUsecase
{
    /**
     * @var Uploader
     */
    protected $uploader;

    /**
     * @param  Uploader $upload
     * @return $this
     */
    public function setUploader(Uploader $uploader)
    {
        $this->uploader = $uploader;
        return $this;
    }

    // Usecase
    public function interact()
    {
        // Fetch the entity, using provided identifiers...
        $entity = $this->getEntity();

        // ... verify that the entity can be deleted by the current user
        $this->verifyDeleteAuth($entity);

        // ... persist the delete
        $this->repo->delete($entity);

        // ... delete uploaded CSV file
        $this->uploader->delete($entity->filename);

        // ... verify that the entity can be read by the current user
        $this->verifyReadAuth($entity);

        // ... and return the formatted entity
        return $this->formatter->__invoke($entity);
    }
}

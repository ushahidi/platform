<?php

/**
 * Ushahidi Posts Export Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2016 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository\Post;

use Ohanzee\DB;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\PostExportRepository;
use Ushahidi\Core\Entity\TagRepository;
use Ushahidi\Core\Entity\SetRepository;
use Ushahidi\App\Repository\CSVPostRepository;
use Ushahidi\Core\Traits\AdminAccess;

class ExportRepository extends CSVPostRepository implements PostExportRepository
{
    use AdminAccess;
    protected $tag_repo;
    protected $set_repo;
    /**
     * @param TagRepository $repo
     */
    public function setTagRepo(TagRepository $repo)
    {
        $this->tag_repo = $repo;
    }

    public function setSetRepo(SetRepository $repo)
    {
        $this->set_repo = $repo;
    }

    /**
     * @param $data
     * @return array
     */
    public function retrieveMetaData($data, $attributes)
    {
        $user = $this->getUser();

        /**
         * Tags (native) should not be shown in the CSV Export
         */
        unset($data['tags']);

        // Set tag labels
        foreach ($data['values'] as $key => $val) {
            // Set attribute names. This is for categories (custom field) to show their label and not the ids
            if (isset($attributes[$key]) && $attributes[$key]['type'] === 'tags') {
                $data['values'][$key] =  $this->tag_repo->getNamesByIds($val);
            }
        }

        // Get contact
        if (!empty($data['contact_id']) &&
                 $this->isUserAdmin($user) ||
                 $this->postPermissions->canUserManagePosts($user)
        ) {
            $contact = $this->contact_repo->get($data['contact_id']);
            $data['contact_type'] = $contact->type;
            $data['contact'] = $contact->contact;
        }

        // Set Form name
        if (!empty($data['form_id'])) {
            $form = $this->form_repo->get($data['form_id']);
            $data['form_name'] = $form->name;
        }

        if (!empty($data['sets'])) {
            $data['sets'] = $this->set_repo->getNamesByIds($data['sets']);
        }

        return $data;
    }

    public function retrieveCompletedStageNames($stage_ids)
    {
        $names = [];
        foreach ($stage_ids as $stage_id) {
            $stage = $this->form_stage_repo->get($stage_id);
            array_push($names, $stage->label);
        }
        return $names;
    }
}

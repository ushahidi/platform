<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Posts Export Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2016 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\PostExportRepository;

class Ushahidi_Repository_Post_Export extends Ushahidi_Repository_CSVPost implements PostExportRepository
{

	/**
	 * @param $data
	 * @return array
	 */
  public function retrieveColumnNameData($data) {

    /**
     * Tags (native) should not be shown in the CSV Export
    */
	  unset($data['tags']);
    // Set attribute keys
    $attributes = [];
  	foreach ($data['values'] as $key => $val)
    {
        $attribute = $this->form_attribute_repo->getByKey($key);
        $attributes[$key] = ['label' => $attribute->label, 'input' => $attribute->input, 'priority'=> $attribute->priority, 'stage' => $attribute->form_stage_id, 'type'=> $attribute->type, 'form_id'=> $data['form_id']];

        // Set attribute names. This is for categories (custom field) to show their label and not the ids
        if ($attribute->type === 'tags') {
          $data['values'][$key] = $this->retrieveTagNames($val);
        }
    }

    $data += ['attributes' => $attributes];


    // Set Set names
    if (!empty($data['sets'])) {
        $data['sets'] = $this->retrieveSetNames($data['sets']);
    }

    // Get contact
    if (!empty($data['contact_id'])) {
        $contact = $this->contact_repo->get($data['contact_id']);
        $data['contact_type'] = $contact->type;
        $data['contact'] = $contact->contact;
    }

    // Set Completed Stage names
    if(!empty($data['completed_stages'])) {
      $data['completed_stages'] = $this->retrieveCompletedStageNames($data['completed_stages']);
    }

    // Set Form name
    if (!empty($data['form_id'])) {
      $form = $this->form_repo->get($data['form_id']);
      $data['form_name'] = $form->name;
    }
    return $data;
  }

  public function retrieveTagNames($tag_ids) {
    $tag_repo = service('repository.tag');
    $names = [];
    foreach($tag_ids as $tag_id) {
      $tag = $tag_repo->get($tag_id);
      array_push($names, $tag->tag);
    }
    return $names;
  }

  public function retrieveSetNames($set_ids) {
    $set_repo = service('repository.set');
    $names = [];
    foreach($set_ids as $set_id) {
      $set = $set_repo->get($set_id);
      array_push($names, $set->name);
    }
    return $names;
  }

  public function retrieveCompletedStageNames($stage_ids) {
    $names = [];
    foreach($stage_ids as $stage_id) {
      $stage = $this->form_stage_repo->get($stage_id);
      array_push($names, $stage->label);
    }
    return $names;
  }
}

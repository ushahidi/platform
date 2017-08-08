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

class Ushahidi_Repository_Post_Export extends Ushahidi_Repository_Post
{

  public function transformData($data) {

    // Set attribute keys
    $attributes = [];
		foreach ($data['values'] as $key => $val)
    {
      $attribute = $this->form_attribute_repo->getByKey($key);
      $attributes[$key] = $attribute->label;

      // Set attribute names
      if ($attribute->type === 'tags') {
        $data['values'][$key] = $this->retrieveTagNames($val);
      }
    }

    $data += ['attributes' => $attributes];

    // Set Set names
    if (!empty($data['sets'])) {
      $data['sets'] = $this->retrieveSetNames($data['sets']);
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

    if (!empty($data['tags'])) {
      $data['tags'] = $this->retrieveTagNames($data['tags']);
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

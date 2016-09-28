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
  protected function getEntity(Array $data = null)
  {
    Kohana::$log->add(Log::ERROR, "arrived in posts export");
    // Append Form Attribute lables indexed by key to post values
    $output = parent::getEntity($data);
    $output += ['attributes' => $this->getFormAttributes($output['values'])];

    return $output;
  }

  protected function getFormAttributes($values) {
    $attributes = [];
		foreach ($values as $key => $val)
    {
      $attribute = $this->form_attribute_repo->getByKey($key);
      $attributes[$key] = $attribute->label;
    }
    return $attributes;
  }
}

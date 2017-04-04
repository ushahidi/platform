<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Varchar Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity\PostValue;
use Ushahidi\Core\Entity\PostValueRepository;

class Ushahidi_Repository_Post_Description extends Ushahidi_Repository_Post_Text
{
  public function getAllForPost($post_id, Array $include_attributes = [], Array $exclude_stages = [], $restricted = false)
	{
    return [];
  }
  // DeleteRepository
  // This value should be immutable and unchangeable
  public function createValue($value, $form_attribute_id, $post_id)
  {
      return 0;
  }

  public function updateValue($id, $value)
  {
      return 0;
  }
}

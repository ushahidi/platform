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

use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\PostRepository as PostRepositoryContract;
use Ushahidi\App\Repository\PostRepository;

class ExportRepository extends PostRepository
{
  public function getFormAttributes($values) {
    $attributes = [];
		foreach ($values as $key => $val)
    {
      $attribute = $this->form_attribute_repo->getByKey($key);
      $attributes[$key] = $attribute->label;
    }
    return $attributes;
  }
}

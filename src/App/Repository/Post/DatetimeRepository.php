<?php

/**
 * Ushahidi Post Varchar Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository\Post;

use Ushahidi\Core\Entity\PostValue;
use Ushahidi\Core\Entity\PostValueRepository as PostValueRepositoryContract;

class DatetimeRepository extends ValueRepository
{
    // OhanzeeRepository
    protected function getTable()
    {
        return 'post_datetime';
    }

    private function convertToMysqlFormat($value)
    {
        $value = date("Y-m-d H:i:s", strtotime($value));
        return $value;
    }

    public function createValue($value, $form_attribute_id, $post_id)
    {
        $value = $this->convertToMysqlFormat($value);
        return parent::createValue($value, $form_attribute_id, $post_id);
    }

    public function updateValue($id, $value)
    {
        $value = $this->convertToMysqlFormat($value);
        return parent::updateValue($id, $value);
    }
}

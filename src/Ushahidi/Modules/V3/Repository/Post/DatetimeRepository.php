<?php

/**
 * Ushahidi Post Varchar Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Repository\Post;

use Ushahidi\Core\Entity\PostValue;

class DatetimeRepository extends ValueRepository
{
    // OhanzeeRepository
    protected function getTable()
    {
        return 'post_datetime';
    }

    // Ushahidi_Repository
    public function getEntity(array $data = null)
    {
        // Replace time with 00:00:00
        if ($this->hideTime && $postDate = date_create($data['value'], new \DateTimeZone('UTC'))) {
            $data['value'] = $postDate->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        }

        return new PostValue($data);
    }

    protected function prepareValue($value)
    {
        return date("Y-m-d H:i:s", strtotime($value));
    }

    protected $hideTime = false;

    public function hideTime($hide = true)
    {
        $this->hideTime = $hide;
    }
}

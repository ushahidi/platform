<?php
/**
 * Ushahidi Post Video Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2016 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Post;

class Video extends ValueValidator
{
    protected function validate($value)
    {
        if (!\Kohana\Validation\Valid::url($value)) {
            return 'url';
        }
        if (!$this->checkVideoTypes($value)) {
            return 'video_type';
        }
    }

    protected function checkVideoTypes($value)
    {
        return (strpos($value, 'youtube') !== false || strpos($value, 'vimeo') !== false);
    }
}

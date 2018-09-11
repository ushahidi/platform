<?php

/**
 * Ushahidi Post Media Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Post;

use Ushahidi\Core\Entity\MediaRepository;

class Media extends ValueValidator
{
    protected $media_repo;

    public function __construct(MediaRepository $media_repo)
    {
        $this->repo = $media_repo;
    }

    protected function validate($value)
    {
        if (!\Kohana\Validation\Valid::digit($value)) {
            return 'digit';
        }

        if (! $this->repo->exists($value)) {
            return 'exists';
        }
    }
}

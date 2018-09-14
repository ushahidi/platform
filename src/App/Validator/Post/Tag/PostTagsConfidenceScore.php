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

use Ushahidi\Core\Entity\ConfidenceScoreRepository;

class PostTagsConfidenceScore extends ValueValidator
{
    protected $repo;

    public function __construct(ConfidenceScoreRepository $score_repo)
    {
        $this->repo = $score_repo;
    }

    protected function validate($value)
    {
        return true;
    }
}

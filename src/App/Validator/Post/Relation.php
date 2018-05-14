<?php

/**
 * Ushahidi Post Relation Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Post;

use Ushahidi\Core\Entity\PostRepository;

class Relation extends ValueValidator
{
    protected $repo;

    public function __construct(PostRepository $repo)
    {
        $this->repo = $repo;
    }

    protected function validate($value)
    {
        if (!\Kohana\Validation\Valid::digit($value)) {
            return 'digit';
        }

        if (! $this->repo->exists($value)) {
            return 'exists';
        }

        $post = $this->repo->get($value);
        if (is_int($this->config['input']['form']) && $post->form_id !== $this->config['input']['form']) {
            return 'invalidForm';
        }
    }
}

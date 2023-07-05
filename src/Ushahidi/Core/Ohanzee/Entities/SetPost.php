<?php

/**
 * Ushahidi Set
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Ohanzee\Entities;

use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\StaticEntity;

class SetPost extends StaticEntity
{
    protected $post_id;
    protected $set_id;
    // DataTransformer
    protected function getDefinition()
    {
        return [
            'post_id' => 'int',
            'set_id' => 'int',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'posts_sets';
    }
}

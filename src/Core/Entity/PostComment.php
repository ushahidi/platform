<?php

/**
 * Ushahidi Form
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class PostComment extends StaticEntity
{
    protected $id;
    protected $post_id;
    protected $user_id;
    protected $comment;

    // DataTransformer
    protected function getDefinition()
    {
        return [
            'id'          => 'int',
            'post'        => false,
            'post_id'     => 'int',
            'user'        => false,
            'user_id'     => 'int',
            'comment'     => 'string'
        ];
    }

    // Entity
    public function getResource()
    {
        return 'comments';
    }

    // StatefulData
    protected function getImmutable()
    {
        return array_merge(parent::getImmutable(), ['user_id', 'post_id']);
    }
}

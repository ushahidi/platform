<?php

/**
 * Ushahidi ConfidenceScore
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class ConfidenceScore extends StaticEntity
{
    protected $id;
    protected $score;
    protected $source;
    protected $post_tag_id;

    // DataTransformer
    protected function getDefinition()
    {
        return [
            'id'      => 'int',
            'score'   => 'float',
            'source'  => 'string',
            'post_tag_id' => 'int',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'confidence_scores';
    }

    protected function getImmutable()
    {
        // Hack: Add computed properties to immutable list
        return array_merge(parent::getImmutable(), ['post_tag_id']);
    }
}

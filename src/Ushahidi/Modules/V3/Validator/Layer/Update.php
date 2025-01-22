<?php

/**
 * Ushahidi Layer Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\Layer;

use Ushahidi\Modules\V3\Validator\LegacyValidator;
use Ushahidi\Contracts\Repository\Entity\MediaRepository;

class Update extends LegacyValidator
{
    protected $media_repo;
    protected $default_error_source = 'layer';

    public function __construct(MediaRepository $media_repo)
    {
        $this->media_repo = $media_repo;
    }

    protected function getRules()
    {
        return [
            'name' => [
                ['min_length', [':value', 2]],
                ['max_length', [':value', 50]],
                // alphas, numbers, punctuation, and spaces
                ['regex', [':value', '/^[\pL\pN\pP ]++$/uD']],
            ],
            'data_url' => [
                ['url']
            ],
            'type' => [
                ['in_array', [':value', ['geojson', 'wms', 'tile']]],
            ],
            'active' => [
                ['in_array', [':value', [0, 1, false, true], true]],
            ],
            'visible_by_default' => [
                ['in_array', [':value', [0, 1, false, true], true]],
            ],
            'media_id' => [
                [[$this->media_repo, 'exists'], [':value']],
            ],
            'options' => [
                ['is_array', [':value']],
            ],
        ];
    }
}

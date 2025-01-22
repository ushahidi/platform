<?php

/**
 * Ushahidi Notification Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\Notification;

use Ushahidi\Modules\V3\Validator\LegacyValidator;
use Ushahidi\Contracts\Repository\Entity\SetRepository;
use Ushahidi\Contracts\Repository\Entity\UserRepository;

class Update extends LegacyValidator
{
    protected $user_repo;
    protected $collection_repo;
    protected $savedsearch_repo;
    protected $default_error_source = 'notification';

    public function __construct(
        UserRepository $user_repo,
        SetRepository $collection_repo,
        SetRepository $savedsearch_repo
    ) {
        $this->user_repo = $user_repo;
        $this->collection_repo = $collection_repo;
        $this->savedsearch_repo = $savedsearch_repo;
    }

    protected function getRules()
    {
        return [
            'id' => [
                ['numeric'],
            ],
            'user_id' => [
                ['numeric'],
                [[$this->user_repo, 'exists'], [':value']],
            ],
            'set_id' => [
                ['numeric'],
                [[$this, 'exists'], [':value']],
            ]
        ];
    }

    public function exists($set_id)
    {
        return $this->collection_repo->exists($set_id) or $this->savedsearch_repo->exists($set_id);
    }
}
